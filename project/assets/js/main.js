/**
 * Study Hub LMS - Main JavaScript
 * Version: 1.0
 */

// =====================================================
// GLOBAL VARIABLES
// =====================================================
let userMenuOpen = false;
let mobileMenuOpen = false;
let notificationMenuOpen = false;

// =====================================================
// DOM READY
// =====================================================
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// =====================================================
// INITIALIZE APPLICATION
// =====================================================
function initializeApp() {
    // Close user menu when clicking outside
    document.addEventListener('click', function(event) {
        const userMenu = document.getElementById('userMenu');
        const userProfile = document.querySelector('.user-profile');
        const notificationMenu = document.getElementById('notificationMenu');
        const notificationIcon = document.querySelector('.notification-icon');
        
        if (userMenu && userProfile) {
            if (!userProfile.contains(event.target) && !userMenu.contains(event.target)) {
                userMenu.style.display = 'none';
                userMenuOpen = false;
            }
        }
        
        if (notificationMenu && notificationIcon) {
            if (!notificationIcon.contains(event.target) && !notificationMenu.contains(event.target)) {
                notificationMenu.style.display = 'none';
                notificationMenuOpen = false;
            }
        }
    });
    
    // Global search functionality
    const globalSearch = document.getElementById('globalSearch');
    if (globalSearch) {
        globalSearch.addEventListener('input', debounce(handleGlobalSearch, 300));
    }
    
    // Initialize file upload areas
    initializeFileUploads();
    
    // Initialize tooltips
    initializeTooltips();
}

// =====================================================
// USER MENU TOGGLE
// =====================================================
function toggleUserMenu() {
    const userMenu = document.getElementById('userMenu');
    if (userMenu) {
        userMenuOpen = !userMenuOpen;
        userMenu.style.display = userMenuOpen ? 'block' : 'none';
    }
}

// =====================================================
// NOTIFICATION MENU TOGGLE
// =====================================================
function toggleNotifications() {
    const notificationMenu = document.getElementById('notificationMenu');
    if (notificationMenu) {
        notificationMenuOpen = !notificationMenuOpen;
        notificationMenu.style.display = notificationMenuOpen ? 'block' : 'none';
    }
}

// =====================================================
// MOBILE MENU TOGGLE
// =====================================================
function toggleMobileMenu() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        mobileMenuOpen = !mobileMenuOpen;
        sidebar.classList.toggle('active');
    }
}

// =====================================================
// GLOBAL SEARCH
// =====================================================
function handleGlobalSearch(event) {
    const query = event.target.value.trim();
    
    if (query.length < 2) {
        hideSearchResults();
        return;
    }
    
    // Implement search functionality
    console.log('Searching for:', query);
    // TODO: Implement AJAX search
}

function hideSearchResults() {
    const resultsContainer = document.getElementById('searchResults');
    if (resultsContainer) {
        resultsContainer.style.display = 'none';
    }
}

// =====================================================
// FILE UPLOAD
// =====================================================
function initializeFileUploads() {
    const uploadAreas = document.querySelectorAll('.file-upload-area');
    
    uploadAreas.forEach(area => {
        const input = area.querySelector('input[type="file"]');
        
        if (!input) return;
        
        // Drag and drop events
        area.addEventListener('dragover', (e) => {
            e.preventDefault();
            area.classList.add('dragover');
        });
        
        area.addEventListener('dragleave', () => {
            area.classList.remove('dragover');
        });
        
        area.addEventListener('drop', (e) => {
            e.preventDefault();
            area.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                input.files = files;
                handleFileSelect(input);
            }
        });
        
        // Click to upload
        area.addEventListener('click', () => {
            input.click();
        });
        
        // File input change
        input.addEventListener('change', () => {
            handleFileSelect(input);
        });
    });
}

function handleFileSelect(input) {
    const files = input.files;
    const fileList = input.closest('.file-upload-area').querySelector('.file-list');
    
    if (!fileList) return;
    
    fileList.innerHTML = '';
    
    Array.from(files).forEach(file => {
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.innerHTML = `
            <i class="fas fa-file"></i>
            <span>${file.name}</span>
            <span class="file-size">${formatFileSize(file.size)}</span>
        `;
        fileList.appendChild(fileItem);
    });
}

// =====================================================
// FORM VALIDATION
// =====================================================
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            showFieldError(field, 'This field is required');
            isValid = false;
        } else {
            clearFieldError(field);
        }
    });
    
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('error');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'form-error';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.classList.remove('error');
    const errorDiv = field.parentNode.querySelector('.form-error');
    if (errorDiv) {
        errorDiv.remove();
    }
}

// =====================================================
// ALERTS & NOTIFICATIONS
// =====================================================
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} fade-in`;
    alertDiv.innerHTML = `
        <i class="fas fa-${getAlertIcon(type)}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" style="margin-left: auto; background: none; border: none; cursor: pointer; font-size: 1.2rem;">&times;</button>
    `;
    
    const container = document.querySelector('.main-content') || document.body;
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

function getAlertIcon(type) {
    const icons = {
        success: 'check-circle',
        error: 'times-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || icons.info;
}

// =====================================================
// MODAL
// =====================================================
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// =====================================================
// CONFIRM DIALOG
// =====================================================
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// =====================================================
// AJAX REQUESTS
// =====================================================
function ajaxRequest(url, method = 'GET', data = null) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open(method, url, true);
        
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    resolve(response);
                } catch (e) {
                    resolve(xhr.responseText);
                }
            } else {
                reject(new Error(xhr.statusText));
            }
        };
        
        xhr.onerror = function() {
            reject(new Error('Network error'));
        };
        
        if (method === 'POST' && data) {
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            const params = new URLSearchParams(data).toString();
            xhr.send(params);
        } else {
            xhr.send();
        }
    });
}

// =====================================================
// UTILITY FUNCTIONS
// =====================================================
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

function formatDate(date) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(date).toLocaleDateString('en-US', options);
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showAlert('Copied to clipboard!', 'success');
    }).catch(() => {
        showAlert('Failed to copy', 'error');
    });
}

function initializeTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(event) {
    const text = event.target.getAttribute('data-tooltip');
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = text;
    tooltip.style.cssText = `
        position: absolute;
        background: #333;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-size: 0.875rem;
        z-index: 10000;
    `;
    document.body.appendChild(tooltip);
    
    const rect = event.target.getBoundingClientRect();
    tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
    tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
    
    event.target._tooltip = tooltip;
}

function hideTooltip(event) {
    if (event.target._tooltip) {
        event.target._tooltip.remove();
        delete event.target._tooltip;
    }
}

// =====================================================
// PROGRESS BAR ANIMATION
// =====================================================
function animateProgressBar(element, targetValue) {
    let currentValue = 0;
    const increment = targetValue / 50;
    
    const interval = setInterval(() => {
        currentValue += increment;
        if (currentValue >= targetValue) {
            currentValue = targetValue;
            clearInterval(interval);
        }
        element.style.width = currentValue + '%';
    }, 20);
}

// Initialize progress bars on page load
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.progress-fill');
    progressBars.forEach(bar => {
        const target = parseFloat(bar.getAttribute('data-progress') || 0);
        bar.style.width = '0%';
        setTimeout(() => {
            animateProgressBar(bar, target);
        }, 100);
    });
});

// =====================================================
// COUNTDOWN TIMER
// =====================================================
function startCountdown(elementId, endDate) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const countdownInterval = setInterval(() => {
        const now = new Date().getTime();
        const distance = new Date(endDate).getTime() - now;
        
        if (distance < 0) {
            clearInterval(countdownInterval);
            element.innerHTML = 'EXPIRED';
            return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        element.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
    }, 1000);
}

// =====================================================
// FORM AUTO-SAVE
// =====================================================
function autoSaveForm(formId, saveUrl) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    const inputs = form.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        input.addEventListener('change', debounce(() => {
            const formData = new FormData(form);
            
            ajaxRequest(saveUrl, 'POST', formData)
                .then(() => {
                    console.log('Form auto-saved');
                })
                .catch(error => {
                    console.error('Auto-save failed:', error);
                });
        }, 2000));
    });
}

// Export functions for global use
window.StudyHub = {
    toggleUserMenu,
    toggleMobileMenu,
    showAlert,
    openModal,
    closeModal,
    confirmAction,
    ajaxRequest,
    validateForm,
    copyToClipboard,
    startCountdown,
    formatFileSize,
    formatDate
};
