{{-- Property Search and Filter Component --}}
<div class="property-search-container mb-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-search"></i> Tìm kiếm & Lọc</h6>
            <button class="btn btn-sm btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#searchCollapse" aria-expanded="false">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="collapse" id="searchCollapse">
            <div class="card-body">
                <form id="propertySearchForm" class="row g-3">
                    {{-- Search by keywords --}}
                    <div class="col-md-4">
                        <label for="searchKeyword" class="form-label">Từ khóa</label>
                        <input type="text" class="form-control" id="searchKeyword" placeholder="Tên, địa chỉ...">
                    </div>

                    {{-- Filter by property category --}}
                    <div class="col-md-4">
                        <label for="filterCategory" class="form-label">Loại bất động sản</label>
                        <select id="filterCategory" class="form-select">
                            <option value="">Tất cả</option>
                            @if(isset($categories) && count($categories) > 0)
                                @foreach($categories as $category)
                                    <option value="{{ $category->CategoryID }}">{{ $category->ten_pro }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    {{-- Filter by price range --}}
                    <div class="col-md-4">
                        <label for="filterPriceRange" class="form-label">Khoảng giá</label>
                        <select id="filterPriceRange" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="0-1000000000">Dưới 1 tỷ</option>
                            <option value="1000000000-3000000000">1 tỷ - 3 tỷ</option>
                            <option value="3000000000-5000000000">3 tỷ - 5 tỷ</option>
                            <option value="5000000000-10000000000">5 tỷ - 10 tỷ</option>
                            <option value="10000000000-999999999999">Trên 10 tỷ</option>
                        </select>
                    </div>

                    {{-- Filter by date range --}}
                    <div class="col-md-4">
                        <label for="filterDateFrom" class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" id="filterDateFrom">
                    </div>

                    <div class="col-md-4">
                        <label for="filterDateTo" class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" id="filterDateTo">
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Tìm kiếm
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-sync-alt"></i> Đặt lại
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle search form submission
    const searchForm = document.getElementById('propertySearchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            filterProperties();
        });

        // Handle form reset
        searchForm.addEventListener('reset', function() {
            setTimeout(() => {
                filterProperties();
            }, 10);
        });
    }

    // Function to filter properties based on search criteria
    function filterProperties() {
        const keyword = document.getElementById('searchKeyword').value.toLowerCase();
        const category = document.getElementById('filterCategory').value;
        const priceRange = document.getElementById('filterPriceRange').value;
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        // Get all property rows
        const propertyRows = document.querySelectorAll('.property-row');
        let visibleCount = 0;

        propertyRows.forEach(row => {
            let shouldShow = true;

            // Filter by keyword (title, address, owner)
            if (keyword) {
                const title = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const address = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                const owner = row.querySelector('td:nth-child(6)').textContent.toLowerCase();

                if (!title.includes(keyword) && !address.includes(keyword) && !owner.includes(keyword)) {
                    shouldShow = false;
                }
            }

            // Filter by category
            if (category && shouldShow) {
                const categoryId = row.getAttribute('data-category');
                if (categoryId !== category) {
                    shouldShow = false;
                }
            }

            // Filter by price range
            if (priceRange && shouldShow) {
                const price = parseFloat(row.getAttribute('data-price') || 0);
                const [minPrice, maxPrice] = priceRange.split('-').map(Number);

                if (price < minPrice || price > maxPrice) {
                    shouldShow = false;
                }
            }

            // Filter by date range
            if (dateFrom && shouldShow) {
                const postedDate = row.getAttribute('data-date');
                if (postedDate < dateFrom) {
                    shouldShow = false;
                }
            }

            if (dateTo && shouldShow) {
                const postedDate = row.getAttribute('data-date');
                if (postedDate > dateTo) {
                    shouldShow = false;
                }
            }

            // Show/hide row based on filters
            row.style.display = shouldShow ? '' : 'none';

            if (shouldShow) {
                visibleCount++;
            }
        });

        // Update property count
        const countElement = document.getElementById('propertyCount');
        if (countElement) {
            countElement.textContent = visibleCount;
        }

        // Show message if no properties match filters
        const noResultsMessage = document.getElementById('noPropertiesMessage');
        if (noResultsMessage) {
            noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }
});
</script>
