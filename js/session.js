document.addEventListener('click', () => {
    const currentDate = new Date();
    var current_time = Math.floor(currentDate.getTime() / 1000); // Convert to seconds

    var timeDifferenceInSeconds = current_time - session_time;

    // Convert 15 minutes to seconds
    var fifteenMinutesInSeconds = 15 * 60;

    if (timeDifferenceInSeconds > fifteenMinutesInSeconds) {
        // Your code to handle the case when the difference is greater than 15 minutes
        document.location = '/fatoora/login/logout';
    } else {
        session_time = current_time;
    }
})

