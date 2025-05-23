@extends('_layout._layadmin.app')

@section('tiepnhanhoso')
    @include('_system.partialview.create_property', [
        'owners' => $owners,
        'categories' => $categories
    ])
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize Bootstrap tabs
        var triggerTabList = [].slice.call(document.querySelectorAll('#propertyTabs button'))
        triggerTabList.forEach(function (triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl)

            triggerEl.addEventListener('click', function (event) {
                event.preventDefault()
                tabTrigger.show()
            })
        });

        // Auto-calculate total area
        $('input[name="HouseLength"], input[name="HouseWidth"]').on('input', function() {
            var length = parseFloat($('input[name="HouseLength"]').val()) || 0;
            var width = parseFloat($('input[name="HouseWidth"]').val()) || 0;
            if (length > 0 && width > 0) {
                $('input[name="TotalLength"]').val(length);
                $('input[name="TotalWidth"]').val(width);
            }
        });

        // Format price input
        $('input[name="Price"]').on('input', function() {
            var value = $(this).val().replace(/\D/g, '');
            $(this).val(value);
        });

        // Show validation feedback
        $('form').on('submit', function(e) {
            var requiredFields = $(this).find('[required]');
            var isValid = true;

            requiredFields.each(function() {
                if (!$(this).val().trim()) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
                // Switch to first tab with errors
                $('#owner-tab').click();
            }
        });
    });
</script>
@endsection
