document.addEventListener('DOMContentLoaded', function() {
    setupFileUpload();
    setupCategorySelect();
});


function setupFileUpload() {
    const dropArea = document.getElementById('dropArea');
    // Updated selector to find the input within the hidden form widget
    const fileInput = dropArea ? dropArea.querySelector('.hidden-form-widget input[type="file"]') : null;
    
    if (!dropArea || !fileInput) return;
    
    const preview = document.getElementById('imagePreview');
    const placeholder = dropArea.querySelector('.upload-placeholder');
    const previewContainer = dropArea.querySelector('.image-preview');
    const removeButton = dropArea.querySelector('.remove-image');
    
    if (!preview || !placeholder || !previewContainer || !removeButton) {
        console.error('Missing required elements for file upload');
        return;
    }
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => {
            dropArea.classList.add('drag-over');
        });
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => {
            dropArea.classList.remove('drag-over');
        });
    });
    
    dropArea.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length) {
            fileInput.files = files;
            handleFiles(files[0]);
        }
    });
    
    fileInput.addEventListener('change', function() {
        if (this.files.length) {
            handleFiles(this.files[0]);
        }
    });
    
    fileInput.addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    function handleFiles(file) {
        if (!file.type.match('image.*')) {
            alert('Please select an image file (JPG, PNG, GIF)');
            return;
        }
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            placeholder.style.display = 'none';
            previewContainer.style.display = 'block';
            removeButton.style.display = 'flex'; 
            dropArea.classList.add('has-image');
            
            // Hide any help text when image is selected
            const helpText = document.querySelector('.image-size-help');
            if (helpText) {
                helpText.style.display = 'none';
            }
        };
        
        reader.readAsDataURL(file);
    }
    
    removeButton.onclick = function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        fileInput.value = '';
        
        preview.src = '';
        placeholder.style.display = 'flex';
        previewContainer.style.display = 'none';
        removeButton.style.display = 'none';
        dropArea.classList.remove('has-image');
        
        // Show any help text when image is removed
        const helpText = document.querySelector('.image-size-help');
        if (helpText) {
            helpText.style.display = 'block';
        }
        
        console.log('Remove button clicked - file removed');
        
        return false;
    };
    
    dropArea.addEventListener('click', function(e) {
        if (e.target === removeButton || removeButton.contains(e.target)) {
            console.log('Clicked on remove button - preventing file dialog');
            return false; 
        }
        
        if (dropArea.classList.contains('has-image')) {
            console.log('Already has image - not opening file dialog');
            return;
        }
        
        fileInput.click();
    });
}


function setupCategorySelect() {
    const select = document.querySelector('.original-select');
    if (!select) return;
    
    const ui = {
        dropdown: document.querySelector('.multiselect-dropdown'),
        container: document.querySelector('.custom-multiselect'),
        tags: document.querySelector('.multiselect-selected-options'),
        search: document.querySelector('.multiselect-search')
    };
    
    select.style.display = 'none';
    createOptions();
    updateTags();
    
    ui.container.onclick = () => {
        ui.dropdown.style.display = 'block';
        ui.search.focus();
    };
    
    document.onclick = (e) => {
        if (!ui.container.contains(e.target)) {
            ui.dropdown.style.display = 'none';
        }
    };
    
    ui.search.onkeyup = () => {
        const searchText = ui.search.value.toLowerCase();
        
        Array.from(ui.dropdown.children).forEach(option => {
            const content = option.textContent.toLowerCase();
            const isSelected = option.classList.contains('selected');
            
            option.style.display = (!isSelected && content.includes(searchText)) ? '' : 'none';
        });
    };
    
    function createOptions() {
        ui.dropdown.innerHTML = '';
        
        Array.from(select.options).forEach(option => {
            const optionEl = document.createElement('div');
            optionEl.className = 'multiselect-option';
            optionEl.textContent = option.text;
            optionEl.setAttribute('data-value', option.value);
            
            if (option.selected) optionEl.classList.add('selected');
            
            optionEl.onclick = function() {
                const value = this.getAttribute('data-value');
                
                Array.from(select.options).forEach(opt => {
                    if (opt.value === value) {
                        opt.selected = true;
                        this.style.display = 'none';
                        ui.search.value = '';
                        updateTags();
                    }
                });
            };
            
            ui.dropdown.appendChild(optionEl);
        });
    }
    
    function updateTags() {
        ui.tags.innerHTML = '';
        
        Array.from(select.options)
            .filter(option => option.selected)
            .forEach(option => {
                const tag = document.createElement('div');
                tag.className = 'selected-option-tag';
                tag.innerHTML = `${option.text} <span class="remove-option">×</span>`;
                tag.setAttribute('data-value', option.value);
                
                tag.querySelector('.remove-option').onclick = function(e) {
                    e.stopPropagation();
                    const value = tag.getAttribute('data-value');
                    
                    Array.from(select.options).forEach(opt => {
                        if (opt.value === value) opt.selected = false;
                    });
                    
                    const dropdownOption = ui.dropdown.querySelector(`[data-value="${value}"]`);
                    if (dropdownOption) dropdownOption.style.display = '';
                    
                    updateTags();
                };
                
                ui.tags.appendChild(tag);
            });
    }
}
