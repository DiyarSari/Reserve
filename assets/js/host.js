(function () {
    'use strict';

    function openHostModals() {
        document.querySelectorAll('[data-host-modal-open]').forEach(function (button) {
            button.addEventListener('click', function (event) {
                if (button.tagName === 'A') {
                    event.preventDefault();
                }
                var modalId = button.getAttribute('data-host-modal-open');
                var modalElement = document.getElementById(modalId);
                if (modalElement && window.bootstrap) {
                    bootstrap.Modal.getOrCreateInstance(modalElement).show();
                }
            });
        });
    }

    function normalizePriceValue(rawValue) {
        var value = String(rawValue || '').trim();
        if (!value) {
            return '';
        }

        value = value.replace(/[^\d,.\-]/g, '');

        if (value.indexOf(',') !== -1 && value.indexOf('.') !== -1) {
            if (value.lastIndexOf(',') > value.lastIndexOf('.')) {
                value = value.replace(/\./g, '').replace(',', '.');
            } else {
                value = value.replace(/,/g, '');
            }
        } else if (value.indexOf(',') !== -1) {
            value = value.replace(',', '.');
        }

        var numeric = Number(value);
        if (!Number.isFinite(numeric)) {
            return '';
        }

        return numeric.toFixed(2);
    }

    function validateTableForm() {
        document.querySelectorAll('.js-table-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                var capacity = form.querySelector('[name="capacity"]');
                var tableNumber = form.querySelector('[name="table_number"]');

                if (tableNumber) {
                    var tableValue = tableNumber.value.trim();
                    var tableValid = /^[A-Za-z0-9\- ]{1,20}$/.test(tableValue);
                    tableNumber.setCustomValidity(tableValid ? '' : 'Masa numarası 1-20 karakter olmalı ve sadece harf/rakam içermelidir.');
                }
                if (capacity) {
                    var capacityValue = Number(capacity.value);
                    var capacityValid = capacityValue >= 1 && capacityValue <= 20;
                    capacity.setCustomValidity(capacityValid ? '' : 'Kapasite 1 ile 20 arasında olmalıdır.');
                }

                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            });
        });
    }

    function attachTableStatusModal() {
        var modalElement = document.getElementById('tableStatusConfirmModal');
        var textElement = document.getElementById('tableStatusConfirmText');
        var confirmButton = document.getElementById('tableStatusConfirmAction');
        if (!modalElement || !confirmButton || !window.bootstrap) {
            return;
        }

        var modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        var pendingForm = null;

        document.querySelectorAll('.js-table-toggle').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                pendingForm = button.closest('form');
                if (!pendingForm) {
                    return;
                }

                var tableNumber = button.getAttribute('data-table-number') || '';
                var nextState = button.getAttribute('data-next-state') || 'active';
                var isTurkish = document.documentElement.lang === 'tr';
                var stateLabel = isTurkish
                    ? (nextState === 'passive' ? 'pasif' : 'aktif')
                    : (nextState === 'passive' ? 'passive' : 'active');

                if (textElement) {
                    if (isTurkish) {
                        textElement.textContent = tableNumber
                            ? ('Masa ' + tableNumber + ' durumu ' + stateLabel + ' olacak. Onaylıyor musun?')
                            : ('Masa durumu ' + stateLabel + ' olacak. Onaylıyor musun?');
                    } else {
                        textElement.textContent = tableNumber
                            ? ('Table ' + tableNumber + ' will be set to ' + stateLabel + '. Do you confirm?')
                            : ('Table status will be set to ' + stateLabel + '. Do you confirm?');
                    }
                }

                modal.show();
            });
        });

        confirmButton.addEventListener('click', function () {
            if (!pendingForm) {
                return;
            }
            modal.hide();
            pendingForm.submit();
            pendingForm = null;
        });
    }

    function attachReservationUiFilter() {
        var table = document.querySelector('.js-host-reservation-table');
        if (!table) {
            return;
        }

        var statusSelect = document.createElement('select');
        statusSelect.className = 'form-select form-select-sm mb-3';
        var storedLang = window.localStorage.getItem('reserveai_lang');
        var isTurkish = storedLang ? storedLang === 'tr' : document.documentElement.lang === 'tr';
        var statusLabels = isTurkish
            ? {
                pending: 'Bekliyor',
                confirmed: 'Onaylandı',
                checked_in: 'Giriş yapıldı',
                completed: 'Tamamlandı',
                cancelled: 'İptal edildi',
                no_show: 'Gelmedi'
            }
            : {
                pending: 'Pending',
                confirmed: 'Confirmed',
                checked_in: 'Checked in',
                completed: 'Completed',
                cancelled: 'Cancelled',
                no_show: 'No-show'
            };

        var allStatusesLabel = isTurkish ? 'Tüm durumlar' : 'All statuses';
        ['all', 'pending', 'confirmed', 'checked_in', 'completed', 'cancelled', 'no_show'].forEach(function (status) {
            var option = document.createElement('option');
            option.value = status === 'all' ? '' : status;
            option.textContent = status === 'all' ? allStatusesLabel : statusLabels[status];
            statusSelect.appendChild(option);
        });

        table.parentNode.insertBefore(statusSelect, table);
        statusSelect.addEventListener('change', function () {
            var value = statusSelect.value;
            table.querySelectorAll('tbody tr').forEach(function (row) {
                var statusBadge = row.querySelector('[data-status]');
                if (!statusBadge) {
                    row.hidden = value !== '';
                    return;
                }

                var rowStatus = statusBadge.getAttribute('data-status');
                row.hidden = value !== '' && rowStatus !== value;
            });
        });
    }

    function attachMenuEditModal() {
        var modal = document.getElementById('menuItemModal');
        if (!modal) {
            return;
        }

        var fields = {
            id: modal.querySelector('#menuEditItemId'),
            name: modal.querySelector('#menuEditName'),
            description: modal.querySelector('#menuEditDescription'),
            price: modal.querySelector('#menuEditPrice'),
            image: modal.querySelector('#menuEditImage'),
            category: modal.querySelector('#menuEditCategory'),
            active: modal.querySelector('#menuEditActive')
        };

        document.querySelectorAll('.js-menu-edit-open').forEach(function (button) {
            button.addEventListener('click', function () {
                if (fields.id) {
                    fields.id.value = button.getAttribute('data-menu-item-id') || '';
                }
                if (fields.name) {
                    fields.name.value = button.getAttribute('data-menu-item-name') || '';
                }
                if (fields.description) {
                    fields.description.value = button.getAttribute('data-menu-item-description') || '';
                }
                if (fields.price) {
                    var rawPrice = button.getAttribute('data-menu-item-price') || '';
                    if (!rawPrice) {
                        var row = button.closest('tr');
                        var priceElement = row ? row.querySelector('.js-menu-item-price') : null;
                        rawPrice = priceElement
                            ? (priceElement.getAttribute('data-price-raw') || priceElement.textContent || '')
                            : '';
                    }
                    fields.price.value = normalizePriceValue(rawPrice);
                }
                if (fields.image) {
                    fields.image.value = button.getAttribute('data-menu-item-image') || '';
                }
                if (fields.category) {
                    fields.category.value = button.getAttribute('data-menu-item-category') || '';
                }
                if (fields.active) {
                    fields.active.checked = button.getAttribute('data-menu-item-active') === '1';
                }
            });
        });
    }

    function initHostDashboard() {
        attachMenuEditModal();
        openHostModals();
        validateTableForm();
        attachTableStatusModal();
        attachReservationUiFilter();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHostDashboard);
    } else {
        initHostDashboard();
    }
})();
