const provinceSelect = document.getElementById('province');
const districtSelect = document.getElementById('district');
const wardSelect = document.getElementById('ward');

// Fetch danh sách tỉnh
async function fetchProvinces(selectedProvince = '') {
    try {
      const res = await fetch('https://provinces.open-api.vn/api/?depth=1');
      if (!res.ok) throw new Error('Không thể tải danh sách tỉnh');
      const provinces = await res.json();

      // Xóa các tùy chọn cũ
      while (provinceSelect.options.length > 0) {
        provinceSelect.remove(0);
      }

      // Thêm tùy chọn mặc định
      const defaultOption = document.createElement('option');
      defaultOption.value = '';
      defaultOption.textContent = 'Chọn Tỉnh';
      provinceSelect.appendChild(defaultOption);

      // Thêm các tùy chọn từ API
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
      console.error(error.message);
      alert('Lỗi khi tải danh sách tỉnh!');
    }
  }

// Fetch danh sách quận theo mã tỉnh
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

      // Xóa các tùy chọn cũ
      while (districtSelect.options.length > 0) {
        districtSelect.remove(0);
      }
      while (wardSelect.options.length > 0) {
        wardSelect.remove(0);
      }

      // Thêm tùy chọn mặc định
      const defaultDistrictOption = document.createElement('option');
      defaultDistrictOption.value = '';
      defaultDistrictOption.textContent = 'Chọn Quận/Huyện';
      districtSelect.appendChild(defaultDistrictOption);

      const defaultWardOption = document.createElement('option');
      defaultWardOption.value = '';
      defaultWardOption.textContent = 'Chọn Phường/Xã';
      wardSelect.appendChild(defaultWardOption);

      // Thêm các tùy chọn từ API
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
      console.error(error.message);
      alert('Lỗi khi tải danh sách quận/huyện!');
    }
  }

// Fetch danh sách phường theo mã quận
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

      // Xóa các tùy chọn cũ
      while (wardSelect.options.length > 0) {
        wardSelect.remove(0);
      }

      // Thêm tùy chọn mặc định
      const defaultOption = document.createElement('option');
      defaultOption.value = '';
      defaultOption.textContent = 'Chọn Phường/Xã';
      wardSelect.appendChild(defaultOption);

      // Thêm các tùy chọn từ API
      data.wards.forEach(w => {
        const option = document.createElement('option');
        option.value = w.name;
        option.textContent = w.name;
        if (w.name === selectedWard) option.selected = true;
        wardSelect.appendChild(option);
      });
    } catch (error) {
      console.error(error.message);
      alert('Lỗi khi tải danh sách phường/xã!');
    }
  }

// Xử lý sự kiện khi chọn tỉnh
provinceSelect.addEventListener('change', () => {
  const provinceName = provinceSelect.value;
  districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
  wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
  if (provinceName) fetchDistricts(provinceName);
});

// Xử lý sự kiện khi chọn quận
districtSelect.addEventListener('change', () => {
  const districtName = districtSelect.value;
  wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
  if (districtName) fetchWards(districtName);
});

document.addEventListener('DOMContentLoaded', async () => {
    const selectedProvince = provinceSelect.getAttribute('data-selected');
    const selectedDistrict = districtSelect.getAttribute('data-selected');
    const selectedWard = wardSelect.getAttribute('data-selected');

    if (selectedProvince || selectedDistrict || selectedWard) {
      // Trường hợp edit
      await fetchProvinces(selectedProvince);
      if (selectedProvince) {
        await fetchDistricts(selectedProvince, selectedDistrict);
      }
      if (selectedDistrict) {
        await fetchWards(selectedDistrict, selectedWard);
      }
    } else {
      // Trường hợp create
      await fetchProvinces();
    }
  });
