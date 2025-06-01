document.addEventListener('DOMContentLoaded', function() {
    // ===== FILE UPLOAD HANDLING =====
    const fileInput = document.querySelector('.file-input');
    if (fileInput) {
        const image = document.querySelector('.file-preview-image');
        const fileName = document.querySelector('.file-preview-name');
        const defaultText = document.querySelector('.file-preview-default');
        const removeButton = document.querySelector('.file-preview-remove');
        const wrapper = document.querySelector('.file-input-wrapper');
        
        // Show preview when file is selected
        fileInput.onchange = function() {
            const file = this.files[0];
            if (file) {
                // Read file and update preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Show file preview
                    defaultText.style.display = 'none';
                    image.src = e.target.result;
                    image.style.display = 'block';
                    fileName.textContent = file.name;
                    fileName.style.display = 'block';
                    removeButton.style.display = 'block';
                    wrapper.classList.add('has-file');
                };
                reader.readAsDataURL(file);
            }
        };
        
        // Remove selected file
        removeButton.onclick = function() {
            fileInput.value = '';
            defaultText.style.display = 'block';
            image.style.display = 'none';
            fileName.style.display = 'none';
            removeButton.style.display = 'none';
            wrapper.classList.remove('has-file');
        };
    }
    
    // ===== CATEGORY MULTISELECT =====
    const select = document.querySelector('.original-select');
    if (select) {
        const dropdown = document.querySelector('.multiselect-dropdown');
        const container = document.querySelector('.custom-multiselect');
        const tagsArea = document.querySelector('.multiselect-selected-options');
        const searchBox = document.querySelector('.multiselect-search');
        
        // Hide original select element
        select.style.display = 'none';
        
        // Create dropdown and update tags
        createOptions();
        updateTags();
        
        // Show dropdown when clicking container
        container.onclick = function() {
            dropdown.style.display = 'block';
            searchBox.focus();
        };
        
        // Hide dropdown when clicking outside
        document.onclick = function(e) {
            if (!container.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        };
        
        // Filter options when typing
        searchBox.onkeyup = function() {
            const text = this.value.toLowerCase();
            
            // Check each option for match
            for (let i = 0; i < dropdown.children.length; i++) {
                const option = dropdown.children[i];
                const content = option.textContent.toLowerCase();
                const isSelected = option.classList.contains('selected');
                
                // Show only unselected options matching search
                option.style.display = (!isSelected && content.includes(text)) ? '' : 'none';
            }
        };
        
        // Create dropdown options
        function createOptions() {
            dropdown.innerHTML = '';
            
            for (let i = 0; i < select.options.length; i++) {
                const option = select.options[i];
                
                // Create option element
                const div = document.createElement('div');
                div.className = 'multiselect-option';
                div.textContent = option.text;
                // Set value as data attribute instead of property
                div.setAttribute('data-value', option.value);
                
                if (option.selected) {
                    div.classList.add('selected');
                }
                
                // Handle option click
                div.onclick = function() {
                    // Find and select matching option
                    for (let j = 0; j < select.options.length; j++) {
                        if (select.options[j].value == this.getAttribute('data-value')) {
                            select.options[j].selected = true;
                            this.style.display = 'none';
                            searchBox.value = '';
                            updateTags();
                            break;
                        }
                    }
                };
                
                dropdown.appendChild(div);
            }
        }
        
        // Update selected tags display
        function updateTags() {
            tagsArea.innerHTML = '';
            
            // Add tag for each selected option
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].selected) {
                    const tag = document.createElement('div');
                    tag.className = 'selected-option-tag';
                    tag.innerHTML = select.options[i].text + ' <span class="remove-option">×</span>';
                    tag.setAttribute('data-value', select.options[i].value);
                    
                    // Remove tag when clicked
                    tag.querySelector('.remove-option').onclick = function(e) {
                        e.stopPropagation();
                        const value = this.parentNode.getAttribute('data-value');
                        
                        // Deselect option
                        for (let j = 0; j < select.options.length; j++) {
                            if (select.options[j].value == value) {
                                select.options[j].selected = false;
                                break;
                            }
                        }
                        
                        // Show option in dropdown again - fixed selector
                        const option = dropdown.querySelector(`[data-value="${value}"]`);
                        if (option) option.style.display = '';
                        
                        updateTags();
                    };
                    
                    tagsArea.appendChild(tag);
                }
            }
        }
    }
});
