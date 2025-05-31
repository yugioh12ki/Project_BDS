/**
 * Transaction Modal Handler
 * Simplified version to ensure the modal works with transaction ID display
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Transaction Modal Handler Loaded');
    setupModalHandlers();
});

function setupModalHandlers() {
    // Simple direct approach - add event listener to the modal itself
    const modal = document.getElementById('transactionDetailsModal');

    if (!modal) {
        console.error('Modal not found: #transactionDetailsModal');
        return;
    }

    // Bootstrap modal event
    modal.addEventListener('show.bs.modal', function(event) {
        console.log('Modal show event triggered');

        // Button that triggered the modal
        const button = event.relatedTarget;
        console.log('Button:', button);

        if (button) {
            const transactionId = button.getAttribute('data-transaction-id');
            console.log('Transaction ID from button:', transactionId);

            // Update the modal title with transaction ID
            const modalIdSpan = document.getElementById('modalTransactionId');
            if (modalIdSpan && transactionId) {
                modalIdSpan.textContent = transactionId;
                console.log('Updated modal title with transaction ID:', transactionId);
            } else {
                console.error('Could not update transaction ID in modal');
                console.log('Modal ID span found:', !!modalIdSpan);
                console.log('Transaction ID found:', !!transactionId);
            }
        } else {
            console.error('No button found in relatedTarget');
        }
    });

    console.log('Modal event handlers set up successfully');
}
