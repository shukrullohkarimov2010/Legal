// Собирает public/css/tw-*.css вместо runtime-компиляции cdn.tailwindcss.com.
// Запуск: npm run build:tw (после правки классов в blade-шаблонах).
import { execFileSync } from 'node:child_process';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');
const cli = path.join(root, 'node_modules/tailwindcss/lib/cli.js');
const input = path.join(root, 'resources/tailwind/input.css');

for (const name of ['default', 'dashboard', 'create', 'analyze', 'calc']) {
    const out = path.join(root, `public/css/tw-${name}.css`);
    execFileSync(process.execPath, [
        cli,
        '-c', path.join(root, `resources/tailwind/${name}.config.js`),
        '-i', input,
        '-o', out,
        '--minify',
    ], { cwd: root, stdio: 'inherit' });
}
