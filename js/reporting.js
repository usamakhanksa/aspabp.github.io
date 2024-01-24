var reporting_invoice_number = 'INV/00000155862';
var reporting_steps = {
    step1: false,
}

document.getElementById('reporting_form').addEventListener('submit', (e) => {
    e.preventDefault();
    reporting_invoice_number = document.getElementById('reporting_invoice_number').value;
    reportInvoice();
})

function reportInvoice() {
    if (document.getElementById('reporting_invoice_number').value != null) {
        invoiceReporting();
    } 
}

function invoiceReporting() {
    fetch(`${api_endpoints.invoice_reporting_path}?invoiceNumber=${reporting_invoice_number}`)
        .then(r => {
            console.log(r.text());

             if (r.status >= 200 && r.status < 400) {
                reporting_steps.step1 = true;
                updateReportingProgress();
                alert('Invoice Reported Successfully')
                resetReportingModal();
            } else {
                alert('Error Occured: Check the Console Log');
            }
        }).catch(e => {
            alert(e);
        });
}


function updateReportingProgress() {
    if (reporting_steps.step1) {
        document.querySelector('#reporting_form .progress__step.progress__step-1').classList.add('active');
    } 
}


function resetReportingModal() {
    showReportingModal();
    document.getElementById('reporting_form').reset();
    reporting_steps.step1 = false;
    document.querySelector('#reporting_form .progress__step.progress__step-1').classList.remove('active');
}


