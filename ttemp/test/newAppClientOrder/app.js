$(document).ready(function() {
    'use strict';

    const DB_KEYS = {
        CLIENTS: 'clientsDB',
        ORDERS: 'ordersDB'
    };

    let clients = [];
    let orders = [];
    let paymentsChart = null;
    let clientsDataTable = null;
    let ordersDataTable = null;
    let clientSelectionDataTable = null;

    function generateId() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    }

    function formatCurrency(amount) {
        return '$' + parseFloat(amount).toFixed(2);
    }

    function formatDate(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    function loadFromLocalStorage() {
        try {
            const clientsData = localStorage.getItem(DB_KEYS.CLIENTS);
            const ordersData = localStorage.getItem(DB_KEYS.ORDERS);
            
            clients = clientsData ? JSON.parse(clientsData) : [];
            orders = ordersData ? JSON.parse(ordersData) : [];
        } catch (error) {
            console.error('Error loading data from localStorage:', error);
            clients = [];
            orders = [];
        }
    }

    // function saveToLocalStorage() {
    //     let new_orders = []
    //     console.log(orders)
    //     orders.forEach(order => {
    //         if (order.paymentStatus != "paid") {
    //             new_orders.push(order)
    //         }
    //     });
    //     orders = new_orders
    //     try {
    //         localStorage.setItem(DB_KEYS.CLIENTS, JSON.stringify(clients));
    //         localStorage.setItem(DB_KEYS.ORDERS, JSON.stringify(orders));
    //     } catch (error) {
    //         console.error('Error saving data to localStorage:', error);
    //         alert('Error saving data. Your browser storage might be full.');
    //     }
    // }

    function initializeClientsDataTable() {
        if (clientsDataTable) {
            clientsDataTable.destroy();
        }

        clientsDataTable = $('#clientsTable').DataTable({
            data: clients,
            columns: [
                { data: 'name', render: escapeHtml },
                { 
                    data: 'phone', 
                    render: function(data) {
                        return data ? escapeHtml(data) : '-';
                    }
                },
                { data: 'createdAt', render: function(data) { return formatDate(data); } },
                { 
                    data: 'id',
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-outline-primary edit-client" data-id="${data}" aria-label="Edit client ${escapeHtml(row.name)}">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-client" data-id="${data}" aria-label="Delete client ${escapeHtml(row.name)}">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        `;
                    }
                }
            ],
            createdRow: function(row, data, dataIndex) {
                $(row).find('td').each(function(index) {
                    const label = $('#clientsTable thead th').eq(index).data('label');
                    $(this).attr('data-label', label);
                });
            },
            language: {
                emptyTable: "No clients found. Add your first client to get started.",
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ clients",
                infoEmpty: "Showing 0 to 0 of 0 clients",
                infoFiltered: "(filtered from _MAX_ total clients)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            pageLength: 10,
            ordering: true,
            searching: true,
            paging: true,
            info: true,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            initComplete: function() {
                addViewToggle('clientsTable');
            }
        });
    }

    function renderClients() {
        initializeClientsDataTable();
    }

    function initializeOrdersDataTable() {
        if (ordersDataTable) {
            ordersDataTable.destroy();
        }

        ordersDataTable = $('#ordersTable').DataTable({
            data: orders,
            columns: [
                { 
                    data: 'clientId',
                    render: function(data) {
                        const client = clients.find(c => c.id === data);
                        return escapeHtml(client ? client.name : 'Unknown Client');
                    }
                },
                { data: 'details', render: escapeHtml },
                { 
                    data: 'amount', 
                    render: function(data) { 
                        return '<strong>' + formatCurrency(data) + '</strong>'; 
                    }
                },
                { 
                    data: 'paymentMethod',
                    render: function(data) {
                        return '<span class="badge bg-primary">' + (data === 'cash' ? 'Cash' : 'App') + '</span>';
                    }
                },
                { 
                    data: 'paymentStatus',
                    render: function(data) {
                        return data === 'paid' 
                            ? '<span class="badge bg-success">Paid</span>'
                            : '<span class="badge bg-warning text-dark">Deferred</span>';
                    }
                },
                { 
                    data: 'orderStage',
                    render: function(data) {
                        const badges = {
                            'pending': '<span class="badge bg-secondary">Pending</span>',
                            'ready': '<span class="badge bg-info">Ready</span>',
                            'received': '<span class="badge bg-success">Received</span>'
                        };
                        return badges[data] || '<span class="badge bg-secondary">Unknown</span>';
                    }
                },
                { data: 'createdAt', render: function(data) { return formatDate(data); } },
                { 
                    data: 'id',
                    orderable: false,
                    render: function(data) {
                        return `
                            <button class="btn btn-sm btn-outline-primary edit-order" data-id="${data}" aria-label="Edit order">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-order" data-id="${data}" aria-label="Delete order">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        `;
                    }
                }
            ],
            createdRow: function(row, data, dataIndex) {
                $(row).find('td').each(function(index) {
                    const label = $('#ordersTable thead th').eq(index).data('label');
                    $(this).attr('data-label', label);
                });
            },
            language: {
                emptyTable: "No orders found. Add your first order to get started.",
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ orders",
                infoEmpty: "Showing 0 to 0 of 0 orders",
                infoFiltered: "(filtered from _MAX_ total orders)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            pageLength: 10,
            ordering: true,
            searching: true,
            paging: true,
            info: true,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            initComplete: function() {
                addViewToggle('ordersTable');
            }
        });
    }

    function renderOrders() {
        initializeOrdersDataTable();
    }

    function updateDashboard() {
        const totalPaid = orders
            .filter(order => order.paymentStatus === 'paid')
            .reduce((sum, order) => sum + parseFloat(order.amount), 0);

        const totalUnpaid = orders
            .filter(order => order.paymentStatus === 'deferred')
            .reduce((sum, order) => sum + parseFloat(order.amount), 0);

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        const dailyProfit = orders
            .filter(order => {
                const orderDate = new Date(order.createdAt);
                orderDate.setHours(0, 0, 0, 0);
                return order.paymentStatus === 'paid' && orderDate.getTime() === today.getTime();
            })
            .reduce((sum, order) => sum + parseFloat(order.amount), 0);

        $('#totalPaid').text(formatCurrency(totalPaid));
        $('#totalUnpaid').text(formatCurrency(totalUnpaid));
        $('#dailyProfit').text(formatCurrency(dailyProfit));
        $('#totalOrders').text(orders.length);

        renderPaymentsChart();
    }

    function renderPaymentsChart() {
        const ctx = document.getElementById('paymentsChart');
        if (!ctx) return;

        const labels = [];
        const data = [];
        
        for (let i = 6; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            date.setHours(0, 0, 0, 0);
            
            const dayLabel = i === 0 ? 'Today' : date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            labels.push(dayLabel);
            
            const dayTotal = orders
                .filter(order => {
                    const orderDate = new Date(order.createdAt);
                    orderDate.setHours(0, 0, 0, 0);
                    return order.paymentStatus === 'paid' && orderDate.getTime() === date.getTime();
                })
                .reduce((sum, order) => sum + parseFloat(order.amount), 0);
            
            data.push(dayTotal);
        }

        if (paymentsChart) {
            paymentsChart.destroy();
        }

        paymentsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Payments Received ($)',
                    data: data,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Amount: ' + formatCurrency(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    }

    function initializeClientSelectionTable() {
        if (clientSelectionDataTable) {
            clientSelectionDataTable.destroy();
        }

        clientSelectionDataTable = $('#clientSelectionTable').DataTable({
            data: clients,
            columns: [
                { data: 'name', render: escapeHtml },
                { 
                    data: 'phone', 
                    render: function(data) {
                        return data ? escapeHtml(data) : '-';
                    }
                },
                { 
                    data: 'id',
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-primary select-this-client" data-id="${data}" data-name="${escapeHtml(row.name)}">
                                <i class="bi bi-check-circle"></i> Select
                            </button>
                        `;
                    }
                }
            ],
            language: {
                emptyTable: "No clients found. Please add a client first.",
                search: "Search clients:",
                lengthMenu: "Show _MENU_ clients",
                info: "Showing _START_ to _END_ of _TOTAL_ clients",
                infoEmpty: "No clients available",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            pageLength: 5,
            ordering: true,
            searching: true,
            paging: true,
            info: true
        });
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    function addViewToggle(tableId) {
        const wrapper = $(`#${tableId}_wrapper`);
        const filterDiv = wrapper.find('.dataTables_filter');
        
        if (filterDiv.find('.view-toggle-wrapper').length > 0) {
            return;
        }

        const toggleHtml = `
            <div class="view-toggle-wrapper">
                <button class="view-toggle-btn active" data-view="table" data-table="${tableId}" title="Table View">
                    <i class="bi bi-table"></i>
                </button>
                <button class="view-toggle-btn" data-view="list" data-table="${tableId}" title="List View">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>
        `;
        
        filterDiv.append(toggleHtml);
    }

    $(document).on('click', '.view-toggle-btn', function() {
        const view = $(this).data('view');
        const tableId = $(this).data('table');
        const table = $(`#${tableId}`);
        
        $(this).siblings('.view-toggle-btn').removeClass('active');
        $(this).addClass('active');
        
        if (view === 'list') {
            table.addClass('list-view');
        } else {
            table.removeClass('list-view');
        }
    });

    function validateClientForm() {
        const form = document.getElementById('clientForm');
        form.classList.add('was-validated');
        return form.checkValidity();
    }

    function validateOrderForm() {
        const form = document.getElementById('orderForm');
        const clientId = $('#orderClient').val();
        const clientNameInput = document.getElementById('selectedClientName');
        
        if (!clientId || clientId.trim() === '') {
            clientNameInput.setCustomValidity('Please select a client.');
        } else {
            clientNameInput.setCustomValidity('');
        }
        
        form.classList.add('was-validated');
        return form.checkValidity();
    }

    $('#addClientBtn').on('click', function() {
        $('#clientForm')[0].reset();
        $('#clientForm').removeClass('was-validated');
        $('#clientId').val('');
        $('#clientModalLabel').text('Add Client');
    });

    $('#clientForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!validateClientForm()) {
            return;
        }

        const clientId = $('#clientId').val();
        const clientData = {
            name: $('#clientName').val().trim(),
            phone: $('#clientPhone').val().trim(),
            // address: $('#clientAddress').val().trim(),
            createdAt: clientId ? clients.find(c => c.id === clientId).createdAt : Date.now()
        };

        if (clientId) {
            const index = clients.findIndex(c => c.id === clientId);
            if (index !== -1) {
                clients[index] = { ...clientData, id: clientId };
            }
        } else {
            clients.push({ ...clientData, id: generateId() });
        }

        saveToLocalStorage();
        renderClients();
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('clientModal'));
        modal.hide();
        
        $('#clientForm')[0].reset();
        $('#clientForm').removeClass('was-validated');
    });

    $(document).on('click', '.edit-client', function() {
        const clientId = $(this).data('id');
        const client = clients.find(c => c.id === clientId);
        
        if (client) {
            $('#clientId').val(client.id);
            $('#clientName').val(client.name);
            $('#clientPhone').val(client.phone || '');
            // $('#clientAddress').val(client.address || '');
            $('#clientModalLabel').text('Edit Client');
            
            const modal = new bootstrap.Modal(document.getElementById('clientModal'));
            modal.show();
        }
    });

    $(document).on('click', '.delete-client', function() {
        const clientId = $(this).data('id');
        const client = clients.find(c => c.id === clientId);
        
        if (client && confirm(`Are you sure you want to delete client "${client.name}"? This will also delete all associated orders.`)) {
            clients = clients.filter(c => c.id !== clientId);
            orders = orders.filter(o => o.clientId !== clientId);
            
            saveToLocalStorage();
            renderClients();
            renderOrders();
            updateDashboard();
        }
    });

    $('#selectClientBtn').on('click', function() {
        initializeClientSelectionTable();
        const modal = new bootstrap.Modal(document.getElementById('clientSelectionModal'));
        modal.show();
    });

    $(document).on('click', '.select-this-client', function() {
        const clientId = $(this).data('id');
        const clientName = $(this).data('name');
        
        $('#orderClient').val(clientId);
        $('#selectedClientName').val(clientName);
        
        document.getElementById('selectedClientName').setCustomValidity('');
        $('#selectedClientName').removeClass('is-invalid');
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('clientSelectionModal'));
        modal.hide();
    });

    $('#addOrderBtn').on('click', function() {
        $('#orderForm')[0].reset();
        $('#orderForm').removeClass('was-validated');
        $('#orderId').val('');
        $('#orderClient').val('');
        $('#selectedClientName').val('');
        $('#orderModalLabel').text('Add Order');
    });

    $('#orderForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!validateOrderForm()) {
            return;
        }

        const orderId = $('#orderId').val();
        const orderData = {
            clientId: $('#orderClient').val(),
            details: $('#orderDetails').val().trim(),
            amount: parseFloat($('#orderAmount').val()),
            paymentMethod: $('#orderPaymentMethod').val(),
            paymentStatus: $('#orderPaymentStatus').val(),
            orderStage: $('#orderStage').val(),
            createdAt: orderId ? orders.find(o => o.id === orderId).createdAt : Date.now()
        };

        if (orderId) {
            const index = orders.findIndex(o => o.id === orderId);
            if (index !== -1) {
                orders[index] = { ...orderData, id: orderId };
            }
        } else {
            orders.push({ ...orderData, id: generateId() });
        }

        saveToLocalStorage();
        renderOrders();
        updateDashboard();
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('orderModal'));
        modal.hide();
        
        $('#orderForm')[0].reset();
        $('#orderForm').removeClass('was-validated');
    });

    $(document).on('click', '.edit-order', function() {
        const orderId = $(this).data('id');
        const order = orders.find(o => o.id === orderId);
        
        if (order) {
            const client = clients.find(c => c.id === order.clientId);
            
            $('#orderId').val(order.id);
            $('#orderClient').val(order.clientId);
            $('#selectedClientName').val(client ? client.name : 'Unknown Client');
            $('#orderDetails').val(order.details);
            $('#orderAmount').val(order.amount);
            $('#orderPaymentMethod').val(order.paymentMethod);
            $('#orderPaymentStatus').val(order.paymentStatus);
            $('#orderStage').val(order.orderStage);
            $('#orderModalLabel').text('Edit Order');
            
            const modal = new bootstrap.Modal(document.getElementById('orderModal'));
            modal.show();
        }
    });

    $(document).on('click', '.delete-order', function() {
        const orderId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this order?')) {
            orders = orders.filter(o => o.id !== orderId);
            
            saveToLocalStorage();
            renderOrders();
            updateDashboard();
        }
    });

    $('#exportBtn').on('click', function(e) {
        e.preventDefault();
        
        const exportData = {
            version: '1.0',
            exportDate: new Date().toISOString(),
            clients: clients,
            orders: orders
        };

        const dataStr = JSON.stringify(exportData, null, 2);
        const dataBlob = new Blob([dataStr], { type: 'application/json' });
        const url = URL.createObjectURL(dataBlob);
        
        const link = document.createElement('a');
        link.href = url;
        link.download = `client-order-backup-${Date.now()}.json`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    });

    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('importFile');
        const file = fileInput.files[0];
        
        if (!file) {
            showImportAlert('Please select a file to import.', 'danger');
            return;
        }

        const reader = new FileReader();
        
        reader.onload = function(event) {
            try {
                const importData = JSON.parse(event.target.result);
                
                if (!validateImportData(importData)) {
                    showImportAlert('Invalid file format. Please select a valid backup file.', 'danger');
                    return;
                }

                const importMode = $('input[name="importMode"]:checked').val();
                
                if (importMode === 'replace') {
                    if (confirm('This will replace all existing data. Are you sure?')) {
                        clients = importData.clients || [];
                        orders = importData.orders || [];
                    } else {
                        return;
                    }
                } else {
                    const existingClientIds = new Set(clients.map(c => c.id));
                    const existingOrderIds = new Set(orders.map(o => o.id));
                    
                    (importData.clients || []).forEach(client => {
                        if (!existingClientIds.has(client.id)) {
                            clients.push(client);
                        }
                    });
                    
                    (importData.orders || []).forEach(order => {
                        if (!existingOrderIds.has(order.id)) {
                            orders.push(order);
                        }
                    });
                }

                saveToLocalStorage();
                renderClients();
                renderOrders();
                updateDashboard();
                
                showImportAlert('Data imported successfully!', 'success');
                
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('importModal'));
                    modal.hide();
                    $('#importForm')[0].reset();
                    $('#importAlert').addClass('d-none');
                }, 1500);
                
            } catch (error) {
                console.error('Import error:', error);
                showImportAlert('Error reading file. Please ensure it is a valid JSON file.', 'danger');
            }
        };
        
        reader.readAsText(file);
    });

    function validateImportData(data) {
        if (!data || typeof data !== 'object') return false;
        if (!Array.isArray(data.clients) || !Array.isArray(data.orders)) return false;
        
        const requiredClientFields = ['id', 'name', 'createdAt'];
        const requiredOrderFields = ['id', 'clientId', 'details', 'amount', 'paymentMethod', 'paymentStatus', 'orderStage', 'createdAt'];
        
        for (const client of data.clients) {
            if (!requiredClientFields.every(field => field in client)) return false;
        }
        
        for (const order of data.orders) {
            if (!requiredOrderFields.every(field => field in order)) return false;
        }
        
        return true;
    }

    function showImportAlert(message, type) {
        const alert = $('#importAlert');
        alert.removeClass('d-none alert-success alert-danger alert-info');
        alert.addClass(`alert-${type}`);
        alert.text(message);
    }

    $('a[href^="#"]').on('click', function(e) {
        const target = $(this).attr('href');
        if (target !== '#' && $(target).length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $(target).offset().top - 70
            }, 500);
        }
    });

    loadFromLocalStorage();
    renderClients();
    renderOrders();
    updateDashboard();
});