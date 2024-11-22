document.addEventListener('DOMContentLoaded', function() {
    if (typeof ReactFormApp !== 'undefined' && ReactFormApp) {
        const container = document.getElementById('react-form-root');
        if (container) {
            ReactFormApp(container);
        } else {
            console.error('React form container not found');
        }
    } else {
        console.error('ReactFormApp not defined');
    }
});