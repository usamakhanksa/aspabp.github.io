var otp = 123345;
var invoice_number = 'INV/00000155862';
var steps = {
    step1: false,
    step2: false,
    step3: false,
}

document.getElementById('onboarding_otp_form').addEventListener('submit', (e) => {
    e.preventDefault();
    otp = document.getElementById('onboarding_otp').value;
    onboard();
})

function onboard() {
    if (document.getElementById('onboarding_otp').value != null) {
        onboardingTemp();
    } 
}

function onboardingTemp() {
    fetch(`${api_endpoints.onboarding_path}?otp=${otp}`)
        .then(r => {
            console.log(r.text());

            if (r.status >= 200 && r.status < 400) {
                steps.step1 = true;
                updateOnboardingProgress();
                onboardingInvoice();
            } else {
                alert('Error Occured: Check the Console Log');
            }
        });
}

function onboardingInvoice() {
    fetch(`${api_endpoints.onboarding_invoice_path}?invoiceNumber=${invoice_number}`)
        .then(r => {
            console.log(r.text());

             if (r.status >= 200 && r.status < 400) {
                steps.step2 = true;
                updateOnboardingProgress();
                onboardingProduction();
            } else {
                alert('Error Occured: Check the Console Log');
            }
        });
}

function onboardingProduction() {
    fetch(`${api_endpoints.onboarding_production_path}`)
        .then(r => {
            console.log(r.text());

             if (r.status >= 200 && r.status < 400) {
                steps.step3 = true;
                updateOnboardingProgress();
                alert('Onboarding Successfull');
            } else {
                alert('Error Occured: Check the Console Log');
            }
        });
}

function updateOnboardingProgress() {
    if (steps.step1) {
        document.querySelector('#onboarding_otp_form .progress__step.progress__step-1').classList.add('active');
    } if (steps.step2) {
        document.querySelector('#onboarding_otp_form .progress__step.progress__step-2').classList.add('active');
    } if (steps.step3) {
        document.querySelector('#onboarding_otp_form .progress__step.progress__step-3').classList.add('active');
    }
}