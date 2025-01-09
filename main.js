document.addEventListener('DOMContentLoaded', function() {
    /*--- Get form elements ---*/
    const roomSelect = document.getElementById('room');
    const featuresCheckboxes = document.querySelectorAll('input[name="features[]"]');
    const arrivalInput = document.getElementById('arrival');
    const departureInput = document.getElementById('departure');
    const totalCostElement = document.getElementById('total-cost');

    /*--- Function to calculate days ---*/
    function calculateDateDifference(arrival, departure) {
        const arrivalDate = new Date(arrival);
        const departureDate = new Date(departure);
        const timeDifference = departureDate - arrivalDate;
        return Math.ceil(timeDifference / (1000 * 3600 * 24) + 1);
    }

    /*--- Function to calculate total cost ----*/
    function updateTotalCost() {
        const roomOption = roomSelect.selectedOptions[0];
        const roomPricePerDay = parseInt(roomOption.getAttribute('data-price'));
        const selectedFeatures = Array.from(featuresCheckboxes).filter(checkbox => checkbox.checked);

        /*--- Calculate total cost of selected feature prices ---*/
        const featurePrice = selectedFeatures.reduce((total, checkbox) => {
            const featurePriceValue = parseInt(checkbox.getAttribute('data-price'));
            return total + (isNaN(featurePriceValue) ? 0 : featurePriceValue);
        }, 0);

        /*--- Get the arrival and departure dates ---*/
        const arrival = arrivalInput.value;
        const departure = departureInput.value;

        if (arrival && departure && roomPricePerDay) {
            /*--- Calculate the number of days ---*/
            const days = calculateDateDifference(arrival, departure);
            if (days > 0) {
                const roomTotal = roomPricePerDay * days;
                const totalPrice = roomTotal + featurePrice;

                /*--- Display total cost ---*/
                totalCostElement.textContent = totalPrice + " coins";
            }
        }
    }

    /*--- Event listeners ---*/
    roomSelect.addEventListener('change', updateTotalCost);
    featuresCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateTotalCost);
    });
    arrivalInput.addEventListener('change', updateTotalCost);
    departureInput.addEventListener('change', updateTotalCost);

    /*--- Total cost calculation ---*/
    updateTotalCost();
});