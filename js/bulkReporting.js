var bulkReportngProcess = false;

document.getElementById('bulk_reporting_form').addEventListener('submit', (e) => {
    e.preventDefault();

    if (!bulkReportngProcess) {
        reportBulkInvoices();
    }
});

function reportBulkInvoices() {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status == 200) {
                let response = xhr.responseText
                console.log(response.message);
            } else {
                console.error('Error: ' + xhr.status);
            }
        }
    };
    xhr.open('GET', '/fatoora/fatoora/bulkReporting.php', true);
    xhr.send();
}
