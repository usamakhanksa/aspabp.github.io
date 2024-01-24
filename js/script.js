function getExistingChangeEventListener(element) {
    const listeners = getEventListeners(element);
    const change_isteners = listeners['change'];

    if (change_isteners && change_isteners.length > 0) {
        // Assuming you have only one change event listener for the element
        return change_isteners[0].listener;
    }

    return null;
}

function getEventListeners(element) {
    if (!element.__eventListeners) {
        element.__eventListeners = {};
    }
    return element.__eventListeners;
}
