const scannerContainer = document.getElementById('scanner-container');
const barcodeInput = document.getElementById('barcode-input');
const scanForm = document.getElementById('scan-form');
const scannerHint = document.getElementById('scanner-hint');

const setScannerHint = function (message) {
    if (scannerHint) {
        scannerHint.textContent = message;
    }
};

if (
    window.Quagga &&
    scannerContainer &&
    barcodeInput &&
    scanForm &&
    navigator.mediaDevices &&
    typeof navigator.mediaDevices.getUserMedia === 'function'
) {
    try {
        setScannerHint('Starting the camera. Point the barcode inside the frame.');

        Quagga.init({
            inputStream: {
                name: 'Live',
                type: 'LiveStream',
                target: scannerContainer,
                constraints: {
                    facingMode: { ideal: 'environment' },
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            },
            decoder: {
                readers: ['ean_reader', 'ean_8_reader', 'code_128_reader']
            }
        }, function (err) {
            if (err) {
                console.error('Quagga init failed:', err);
                setScannerHint('Camera access failed. You can still enter the barcode manually below.');
                return;
            }

            Quagga.start();
            setScannerHint('Align the barcode inside the frame. The scan will submit automatically.');
        });

        Quagga.onDetected(function (result) {
            const barcode = result && result.codeResult ? result.codeResult.code : null;

            if (!barcode) {
                return;
            }

            Quagga.stop();
            setScannerHint('Barcode detected. Checking product...');
            barcodeInput.value = barcode;
            scanForm.submit();
        });
    } catch (error) {
        console.error('Scanner startup failed:', error);
        setScannerHint('The camera could not start. Enter the barcode manually below.');
    }
} else if (scannerHint) {
    setScannerHint('Camera scanning is available on supported mobile browsers with HTTPS or localhost.');
}
