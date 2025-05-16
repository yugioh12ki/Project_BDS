// dscactinh_edit.js - Dùng riêng cho modal edit user

document.addEventListener('shown.bs.modal', function (event) {
    if (event.target.id && event.target.id.startsWith('editModal')) {
        const modal = event.target;
        const provinceSelect = modal.querySelector('#province');
        const districtSelect = modal.querySelector('#district');
        const wardSelect = modal.querySelector('#ward');
        if (!(provinceSelect && districtSelect && wardSelect)) return;

        // Hàm fetch danh sách tỉnh
        async function fetchProvinces(selectedProvince = '') {
            try {
                const res = await fetch('https://provinces.open-api.vn/api/?depth=1');
                if (!res.ok) throw new Error('Không thể tải danh sách tỉnh');
                const provinces = await res.json();
                provinceSelect.innerHTML = '<option value="">Chọn Tỉnh</option>';
                provinces.forEach(p => {
                    const option = document.createElement('option');
                    option.value = p.name;
                    option.textContent = p.name;
                    option.setAttribute('data-code', p.code);
                    if (p.name === selectedProvince) option.selected = true;
                    provinceSelect.appendChild(option);
                });
                if (selectedProvince) {
                    await fetchDistricts(selectedProvince);
                }
            } catch (error) {
                alert('Lỗi khi tải danh sách tỉnh!');
            }
        }

        // Hàm fetch danh sách quận
        async function fetchDistricts(provinceName, selectedDistrict = '') {
            try {
                const selectedOption = Array.from(provinceSelect.options).find(
                    option => option.value === provinceName
                );
                const provinceCode = selectedOption ? selectedOption.getAttribute('data-code') : null;
                if (!provinceCode) return;
                const res = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
                if (!res.ok) throw new Error('Không thể tải danh sách quận/huyện');
                const data = await res.json();
                districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
                wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                data.districts.forEach(d => {
                    const option = document.createElement('option');
                    option.value = d.name;
                    option.textContent = d.name;
                    option.setAttribute('data-code', d.code);
                    if (d.name === selectedDistrict) option.selected = true;
                    districtSelect.appendChild(option);
                });
                if (selectedDistrict) {
                    await fetchWards(selectedDistrict);
                }
            } catch (error) {
                alert('Lỗi khi tải danh sách quận/huyện!');
            }
        }

        // Hàm fetch danh sách phường
        async function fetchWards(districtName, selectedWard = '') {
            try {
                const selectedOption = Array.from(districtSelect.options).find(
                    option => option.value === districtName
                );
                const districtCode = selectedOption ? selectedOption.getAttribute('data-code') : null;
                if (!districtCode) return;
                const res = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
                if (!res.ok) throw new Error('Không thể tải danh sách phường/xã');
                const data = await res.json();
                wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                data.wards.forEach(w => {
                    const option = document.createElement('option');
                    option.value = w.name;
                    option.textContent = w.name;
                    if (w.name === selectedWard) option.selected = true;
                    wardSelect.appendChild(option);
                });
            } catch (error) {
                alert('Lỗi khi tải danh sách phường/xã!');
            }
        }

        // Sự kiện khi chọn lại tỉnh
        provinceSelect.addEventListener('change', function () {
            const provinceName = provinceSelect.value;
            districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
            wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
            if (provinceName) fetchDistricts(provinceName);
        });
        // Sự kiện khi chọn lại quận
        districtSelect.addEventListener('change', function () {
            const districtName = districtSelect.value;
            wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
            if (districtName) fetchWards(districtName);
        });

        // Khởi tạo khi modal mở
        const selectedProvince = provinceSelect.getAttribute('data-selected');
        const selectedDistrict = districtSelect.getAttribute('data-selected');
        const selectedWard = wardSelect.getAttribute('data-selected');
        (async () => {
            await fetchProvinces(selectedProvince);
            if (selectedProvince) {
                await fetchDistricts(selectedProvince, selectedDistrict);
                if (selectedDistrict) {
                    await fetchWards(selectedDistrict, selectedWard);
                }
            }
        })();
    }
});
