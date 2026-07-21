import * as faceapi from 'face-api.js';

const video = document.getElementById('camera');
const cameraPanel = document.getElementById('cameraPanel');
const placeholder = document.getElementById('cameraPlaceholder');
const startButton = document.getElementById('startScan');
const statusBox = document.getElementById('scanStatus');
const statusText = document.getElementById('scanStatusText');
const confirmForm = document.getElementById('confirmScanForm');
const faceSignatureInput = document.getElementById('faceSignature');
const config = window.FaceVerification || {};
const messages = config.messages || {};

let stream = null;
let modelsReady = null;

const requiredSamples = 5;
const modelUrl = config.modelUrl || '/models';
const detectorOptions = new faceapi.TinyFaceDetectorOptions({
    inputSize: 224,
    scoreThreshold: 0.55,
});

function setStatus(message, type = '') {
    statusText.textContent = message;
    statusBox.className = `status ${type}`.trim();
}

function stopCamera() {
    if (!stream) {
        return;
    }

    stream.getTracks().forEach((track) => track.stop());
    stream = null;
    video.srcObject = null;
    placeholder.style.display = 'grid';
}

async function startCamera() {
    setStatus(messages.cameraStarting);
    stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } },
        audio: false,
    });

    video.srcObject = stream;
    await video.play();
    placeholder.style.display = 'none';
}

async function loadModels() {
    modelsReady = modelsReady || Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri(modelUrl),
        faceapi.nets.faceLandmark68Net.loadFromUri(modelUrl),
        faceapi.nets.faceRecognitionNet.loadFromUri(modelUrl),
    ]);

    return modelsReady;
}

function descriptorToArray(descriptor) {
    return Array.from(descriptor, (value) => Math.round(value * 1000000) / 1000000);
}

function averageDescriptors(samples) {
    const length = samples[0].length;
    const averaged = [];

    for (let index = 0; index < length; index++) {
        const sum = samples.reduce((total, sample) => total + sample[index], 0);
        averaged.push(Math.round((sum / samples.length) * 1000000) / 1000000);
    }

    return averaged;
}

function euclideanDistance(left, right) {
    const sum = left.reduce((total, value, index) => total + ((value - right[index]) ** 2), 0);

    return Math.sqrt(sum);
}

function samplesAreStable(samples, averaged) {
    return samples.every((sample) => euclideanDistance(sample, averaged) <= 0.35);
}

function faceIsWellFramed(detection) {
    const box = detection.detection.box;
    const videoWidth = video.videoWidth || 1;
    const videoHeight = video.videoHeight || 1;
    const faceWidthRatio = box.width / videoWidth;
    const faceHeightRatio = box.height / videoHeight;
    const centerX = box.x + box.width / 2;
    const centerY = box.y + box.height / 2;
    const horizontalOffset = Math.abs(centerX - (videoWidth / 2)) / videoWidth;
    const verticalOffset = Math.abs(centerY - (videoHeight / 2)) / videoHeight;

    return faceWidthRatio >= 0.16
        && faceHeightRatio >= 0.20
        && faceWidthRatio <= 0.76
        && faceHeightRatio <= 0.9
        && horizontalOffset <= 0.24
        && verticalOffset <= 0.26;
}

async function captureDescriptors(deadline) {
    const descriptors = [];

    while (Date.now() < deadline && descriptors.length < requiredSamples) {
        const detection = await faceapi
            .detectSingleFace(video, detectorOptions)
            .withFaceLandmarks()
            .withFaceDescriptor();

        if (detection && faceIsWellFramed(detection)) {
            descriptors.push(descriptorToArray(detection.descriptor));
            setStatus(messages.faceFound, 'success');
        }

        await new Promise((resolve) => setTimeout(resolve, 260));
    }

    if (descriptors.length < requiredSamples) {
        return null;
    }

    const signature = averageDescriptors(descriptors);

    return samplesAreStable(descriptors, signature)
        ? { signature, samples: descriptors }
        : null;
}

startButton.addEventListener('click', async () => {
    startButton.disabled = true;
    cameraPanel.classList.add('is-scanning');

    try {
        if (!navigator.mediaDevices?.getUserMedia) {
            throw new Error(messages.unsupported || 'Camera API is unavailable');
        }

        setStatus(messages.modelsLoading || messages.scanning);
        await loadModels();
        await startCamera();
        setStatus(messages.scanning);

        const result = await captureDescriptors(Date.now() + 12000);

        if (!result) {
            throw new Error(messages.noFace);
        }

        faceSignatureInput.value = JSON.stringify({
            version: 3,
            model: 'face-api.js',
            descriptor_size: result.signature.length,
            signature: result.signature,
            samples: result.samples,
        });

        setStatus(messages.success, 'success');
        stopCamera();
        setTimeout(() => confirmForm.submit(), 450);
    } catch (error) {
        stopCamera();
        setStatus(error.message || messages.cameraError, 'error');
        startButton.disabled = false;
    } finally {
        cameraPanel.classList.remove('is-scanning');
    }
});

window.addEventListener('beforeunload', stopCamera);
