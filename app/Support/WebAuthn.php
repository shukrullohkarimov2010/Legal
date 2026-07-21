<?php

namespace App\Support;

use InvalidArgumentException;

class WebAuthn
{
    public static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    public static function base64UrlDecode(string $value): string
    {
        $decoded = base64_decode(strtr($value, '-_', '+/').str_repeat('=', (4 - strlen($value) % 4) % 4), true);

        if ($decoded === false) {
            throw new InvalidArgumentException('Invalid base64url value.');
        }

        return $decoded;
    }

    /**
     * @return array{credentialId: string, publicKeyPem: string, alg: int, signCount: int}
     */
    public static function parseAttestationObject(string $attestationObject, string $expectedRpId): array
    {
        $attestation = self::decodeCbor($attestationObject);
        $authData = $attestation['authData'] ?? null;

        if (! is_string($authData) || strlen($authData) < 55) {
            throw new InvalidArgumentException('Invalid authenticator data.');
        }

        self::assertAuthenticatorFlags($authData, true);
        self::assertRpIdHash($authData, $expectedRpId);

        $signCount = self::readSignCount($authData);
        $offset = 37 + 16;
        $credentialIdLength = unpack('n', substr($authData, $offset, 2))[1];
        $offset += 2;
        $credentialId = substr($authData, $offset, $credentialIdLength);
        $offset += $credentialIdLength;
        $credentialPublicKey = substr($authData, $offset);
        $coseKey = self::decodeCbor($credentialPublicKey);
        $alg = (int) ($coseKey[3] ?? 0);

        return [
            'credentialId' => $credentialId,
            'publicKeyPem' => self::coseKeyToPem($coseKey),
            'alg' => $alg,
            'signCount' => $signCount,
        ];
    }

    public static function verifyAssertionSignature(
        string $publicKeyPem,
        int $alg,
        string $authenticatorData,
        string $clientDataJson,
        string $signature,
        string $expectedRpId
    ): bool {
        self::assertAuthenticatorFlags($authenticatorData);
        self::assertRpIdHash($authenticatorData, $expectedRpId);

        $signedData = $authenticatorData.hash('sha256', $clientDataJson, true);
        $verificationSignature = $alg === -7 ? self::ecdsaRawSignatureToDer($signature) : $signature;

        return openssl_verify($signedData, $verificationSignature, $publicKeyPem, OPENSSL_ALGO_SHA256) === 1;
    }

    public static function readSignCount(string $authenticatorData): int
    {
        return unpack('N', substr($authenticatorData, 33, 4))[1];
    }

    /**
     * @return mixed
     */
    public static function decodeCbor(string $data): mixed
    {
        $offset = 0;
        $decoded = self::decodeCborItem($data, $offset);

        if ($offset > strlen($data)) {
            throw new InvalidArgumentException('Invalid CBOR payload.');
        }

        return $decoded;
    }

    private static function assertAuthenticatorFlags(string $authenticatorData, bool $requireAttestation = false): void
    {
        if (strlen($authenticatorData) < 37) {
            throw new InvalidArgumentException('Authenticator data is too short.');
        }

        $flags = ord($authenticatorData[32]);
        $userPresent = ($flags & 0x01) === 0x01;
        $userVerified = ($flags & 0x04) === 0x04;
        $hasAttestedCredentialData = ($flags & 0x40) === 0x40;

        if (! $userPresent || ! $userVerified || ($requireAttestation && ! $hasAttestedCredentialData)) {
            throw new InvalidArgumentException('User verification is required.');
        }
    }

    private static function assertRpIdHash(string $authenticatorData, string $expectedRpId): void
    {
        if (! hash_equals(hash('sha256', $expectedRpId, true), substr($authenticatorData, 0, 32))) {
            throw new InvalidArgumentException('Invalid relying party hash.');
        }
    }

    /**
     * @param  array<int|string, mixed>  $coseKey
     */
    private static function coseKeyToPem(array $coseKey): string
    {
        $keyType = (int) ($coseKey[1] ?? 0);
        $alg = (int) ($coseKey[3] ?? 0);

        if ($keyType === 2 && $alg === -7) {
            $x = $coseKey[-2] ?? null;
            $y = $coseKey[-3] ?? null;

            if (! is_string($x) || ! is_string($y) || strlen($x) !== 32 || strlen($y) !== 32) {
                throw new InvalidArgumentException('Invalid EC public key.');
            }

            $algorithm = self::derSequence(
                self::derOid('1.2.840.10045.2.1'),
                self::derOid('1.2.840.10045.3.1.7')
            );
            $publicKey = self::derBitString("\x04".$x.$y);

            return self::pem('PUBLIC KEY', self::derSequence($algorithm, $publicKey));
        }

        if ($keyType === 3 && $alg === -257) {
            $n = $coseKey[-1] ?? null;
            $e = $coseKey[-2] ?? null;

            if (! is_string($n) || ! is_string($e)) {
                throw new InvalidArgumentException('Invalid RSA public key.');
            }

            $algorithm = self::derSequence(self::derOid('1.2.840.113549.1.1.1'), "\x05\x00");
            $publicKey = self::derBitString(self::derSequence(self::derInteger($n), self::derInteger($e)));

            return self::pem('PUBLIC KEY', self::derSequence($algorithm, $publicKey));
        }

        throw new InvalidArgumentException('Unsupported authenticator key.');
    }

    private static function ecdsaRawSignatureToDer(string $signature): string
    {
        if (strlen($signature) !== 64) {
            return $signature;
        }

        return self::derSequence(
            self::derInteger(substr($signature, 0, 32)),
            self::derInteger(substr($signature, 32, 32))
        );
    }

    /**
     * @return mixed
     */
    private static function decodeCborItem(string $data, int &$offset): mixed
    {
        if ($offset >= strlen($data)) {
            throw new InvalidArgumentException('Unexpected end of CBOR data.');
        }

        $initial = ord($data[$offset++]);
        $major = $initial >> 5;
        $additional = $initial & 0x1f;
        $length = self::readCborLength($data, $offset, $additional);

        return match ($major) {
            0 => $length,
            1 => -1 - $length,
            2 => self::readCborBytes($data, $offset, $length),
            3 => self::readCborBytes($data, $offset, $length),
            4 => self::readCborArray($data, $offset, $length),
            5 => self::readCborMap($data, $offset, $length),
            6 => self::decodeCborItem($data, $offset),
            7 => match ($additional) {
                20 => false,
                21 => true,
                22, 23 => null,
                default => throw new InvalidArgumentException('Unsupported CBOR simple value.'),
            },
            default => throw new InvalidArgumentException('Unsupported CBOR major type.'),
        };
    }

    private static function readCborLength(string $data, int &$offset, int $additional): int
    {
        if ($additional < 24) {
            return $additional;
        }

        $sizes = [24 => 1, 25 => 2, 26 => 4, 27 => 8];
        $size = $sizes[$additional] ?? null;

        if ($size === null || $offset + $size > strlen($data)) {
            throw new InvalidArgumentException('Invalid CBOR length.');
        }

        $bytes = substr($data, $offset, $size);
        $offset += $size;

        if ($size === 8) {
            $parts = unpack('Nhigh/Nlow', $bytes);

            return ($parts['high'] << 32) + $parts['low'];
        }

        return match ($size) {
            1 => ord($bytes),
            2 => unpack('n', $bytes)[1],
            4 => unpack('N', $bytes)[1],
        };
    }

    private static function readCborBytes(string $data, int &$offset, int $length): string
    {
        if ($offset + $length > strlen($data)) {
            throw new InvalidArgumentException('Invalid CBOR byte string.');
        }

        $value = substr($data, $offset, $length);
        $offset += $length;

        return $value;
    }

    /**
     * @return array<int, mixed>
     */
    private static function readCborArray(string $data, int &$offset, int $length): array
    {
        $items = [];

        for ($index = 0; $index < $length; $index++) {
            $items[] = self::decodeCborItem($data, $offset);
        }

        return $items;
    }

    /**
     * @return array<int|string, mixed>
     */
    private static function readCborMap(string $data, int &$offset, int $length): array
    {
        $items = [];

        for ($index = 0; $index < $length; $index++) {
            $key = self::decodeCborItem($data, $offset);
            $items[is_int($key) ? $key : (string) $key] = self::decodeCborItem($data, $offset);
        }

        return $items;
    }

    private static function pem(string $label, string $der): string
    {
        return "-----BEGIN {$label}-----\n"
            .chunk_split(base64_encode($der), 64, "\n")
            ."-----END {$label}-----\n";
    }

    private static function derSequence(string ...$items): string
    {
        $body = implode('', $items);

        return "\x30".self::derLength(strlen($body)).$body;
    }

    private static function derInteger(string $bytes): string
    {
        $bytes = ltrim($bytes, "\x00");
        $bytes = $bytes === '' ? "\x00" : $bytes;

        if ((ord($bytes[0]) & 0x80) === 0x80) {
            $bytes = "\x00".$bytes;
        }

        return "\x02".self::derLength(strlen($bytes)).$bytes;
    }

    private static function derBitString(string $bytes): string
    {
        $body = "\x00".$bytes;

        return "\x03".self::derLength(strlen($body)).$body;
    }

    private static function derOid(string $oid): string
    {
        $parts = array_map('intval', explode('.', $oid));
        $body = chr(($parts[0] * 40) + $parts[1]);

        foreach (array_slice($parts, 2) as $part) {
            $encoded = chr($part & 0x7f);
            $part >>= 7;

            while ($part > 0) {
                $encoded = chr(($part & 0x7f) | 0x80).$encoded;
                $part >>= 7;
            }

            $body .= $encoded;
        }

        return "\x06".self::derLength(strlen($body)).$body;
    }

    private static function derLength(int $length): string
    {
        if ($length < 128) {
            return chr($length);
        }

        $bytes = '';

        while ($length > 0) {
            $bytes = chr($length & 0xff).$bytes;
            $length >>= 8;
        }

        return chr(0x80 | strlen($bytes)).$bytes;
    }
}
