@php
    $isEdit = isset($salesDo);
    $existingItems = [];

    if ($isEdit) {
        $existingItems = $salesDo->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'qty_ordered' => $item->qty_ordered,
                'unit_price' => $item->unit_price,
                'discount_percent' => $item->discount_percent,
            ];
        })->values()->toArray();
    } elseif (old('items')) {
        // Preserve old input when validation fails
        $existingItems = collect(old('items'))->map(function ($item) {
            return [
                'product_id' => $item['product_id'] ?? '',
                'qty_ordered' => $item['qty_ordered'] ?? 1,
                'unit_price' => $item['unit_price'] ?? 0,
                'discount_percent' => $item['discount_percent'] ?? 0,
            ];
        })->values()->toArray();
    }
@endphp

@push('styles')
    <style>
        /* remove arrow input number */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@endpush

<div class="space-y-6">
    <!-- Basic Information -->
    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Basic Information</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @if($isEdit)
                <!-- DO Code (Read-only) -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium dark:text-white">DO Code</label>
                    <input type="text" readonly
                        value="{{ $salesDo->do_code }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800 dark:text-white dark:border-gray-700 dark:bg-gray-800">
                </div>

                <!-- Status (Read-only) -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium dark:text-white">Status</label>
                    <span class="inline-flex px-3 py-2.5 rounded-lg text-sm font-medium dark:text-white
                        @switch($salesDo->status)
                            @case('crm_to_wqs')
                                bg-warning-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                @break
                            @case('wqs_on_hold')
                                bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                @break
                            @default
                                bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300
                        @endswitch">
                        {{ match($salesDo->status) {
                            'crm_to_wqs' => 'CRM to WQS',
                            'wqs_on_hold' => 'WQS On Hold',
                            default => ucfirst($salesDo->status)
                        } }}
                    </span>
                </div>
            @endif

            <!-- Customer -->
            <div>
                <label class="mb-1.5 block text-sm font-medium dark:text-white">
                    Customer <span class="text-red-500">*</span>
                </label>

                <x-form.select.searchable-select
                    name="customer_id"
                    id="customerSelect"
                    :options="$customers->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()"
                    :selected="old('customer_id', $isEdit ? $salesDo->customer_id : '')"
                    placeholder="-- Select Customer --"
                    searchPlaceholder="Search customer..."
                    :required="true"
                />

                @error('customer_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Office -->
            <div>
                <label class="mb-1.5 block text-sm font-medium dark:text-white">
                    Office <span class="text-red-500">*</span>
                </label>

                <x-form.select.searchable-select
                    name="office_id"
                    :options="$offices->map(fn($o) => ['value' => $o->id, 'label' => $o->name])->toArray()"
                    :selected="old('office_id', $isEdit ? $salesDo->office_id : '')"
                    placeholder="-- Select Office --"
                    searchPlaceholder="Search office..."
                    :required="true"
                />

                @error('office_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- DO Date -->
            <div>
                <label class="mb-1.5 block text-sm font-medium dark:text-white">
                    DO Date <span class="text-red-500">*</span>
                </label>

                <x-form.date-picker
                    id="do_date"
                    name="do_date"
                    placeholder="Select DO Date"
                    :defaultDate="old(
                        'do_date',
                        $isEdit ? $salesDo->do_date->toDateString() : now()->toDateString()
                    )"
                />

                @error('do_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Payment Term -->
            <div>
                <label class="mb-1.5 block text-sm font-medium dark:text-white">Payment Term</label>

                <x-form.select.searchable-select
                    name="payment_term_id"
                    :options="$paymentTerms->map(fn($t) => ['value' => $t->id, 'label' => $t->name])->toArray()"
                    :selected="old('payment_term_id', $isEdit ? $salesDo->payment_term_id : '')"
                    placeholder="Select Payment Term"
                    searchPlaceholder="Search payment term..."
                />

                @error('payment_term_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- PIC Customer -->
            <div>
                <label class="mb-1.5 block text-sm font-medium dark:text-white">PIC Customer</label>
                <input type="text" name="pic_customer" maxlength="100" placeholder="Person In Charge"
                    value="{{ old('pic_customer', $isEdit ? $salesDo->pic_customer : '') }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">
                @error('pic_customer') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Tax -->
            <div>
                <label class="mb-1.5 block text-sm font-medium dark:text-white">Tax</label>

                <x-form.select.searchable-select
                    name="tax_id"
                    id="taxSelect"
                    :options="$taxes->map(fn($t) => ['value' => $t->id, 'label' => $t->name . ' (' . $t->rate . '%)'])->toArray()"
                    :selected="old('tax_id', $isEdit ? $salesDo->tax_id : '')"
                    placeholder="Select Tax"
                    searchPlaceholder="Search tax..."
                />

                @error('tax_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="mt-6">
            <label class="mb-1.5 block text-sm font-medium dark:text-white">
                Shipping Address <span class="text-red-500">*</span>
            </label>
            <textarea name="shipping_address" rows="3" required placeholder="Enter complete shipping address"
                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">{{ old('shipping_address', $isEdit ? $salesDo->shipping_address : '') }}</textarea>
            @error('shipping_address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Notes -->
        <div class="mt-6">
            <label class="mb-1.5 block text-sm font-medium dark:text-white">Notes (CRM)</label>
            <textarea name="notes_crm" rows="3" placeholder="Any special notes or instructions"
                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 dark:text-white focus:border-blue-300 focus:ring-3 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900">{{ old('notes_crm', $isEdit ? $salesDo->notes_crm : '') }}</textarea>
            @error('notes_crm') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <!-- Items Section -->
    <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mt-3 h-full ">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Order Items</h3>
            <button type="button" id="addItemBtn"
                class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Item
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-2 px-2 font-medium text-gray-700 dark:text-gray-300 w-12">#</th>
                        <th class="text-left py-2 px-2 font-medium text-gray-700 dark:text-gray-300" style="min-width: 250px;">Product</th>
                        <th class="text-right py-2 px-2 font-medium text-gray-700 dark:text-gray-300" style="min-width: 140px;">Qty / Stock</th>
                        <th class="text-right py-2 px-2 font-medium text-gray-700 dark:text-gray-300" style="min-width: 120px;">Unit Price</th>
                        <th class="text-right py-2 px-2 font-medium text-gray-700 dark:text-gray-300" style="min-width: 120px;">Discount %</th>
                        <th class="text-right py-2 px-2 font-medium text-gray-700 dark:text-gray-300" style="min-width: 150px;">Line Total</th>
                        <th class="text-center py-2 px-2 font-medium text-gray-700 dark:text-gray-300 w-24">Action</th>
                    </tr>
                </thead>
                <tbody id="itemsContainer">
                    <!-- Items will be added here by JavaScript -->
                </tbody>
            </table>
        </div>

        @error('items')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
        @if($errors->has('items.*'))
            <p class="mt-2 text-sm text-red-600">Please check all items for errors</p>
        @endif
    </div>

    <!-- Summary Section -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 space-y-3 border border-gray-200 dark:border-gray-700">
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-white">Subtotal:</span>
            <span class="font-medium text-gray-800 dark:text-white" id="subtotalDisplay">Rp 0</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-white">Tax:</span>
            <span class="font-medium text-gray-800 dark:text-white" id="taxDisplay">Rp 0</span>
        </div>
        <div class="border-t border-gray-200 dark:border-white/50 pt-3 flex justify-between">
            <span class="text-lg font-semibold text-gray-800 dark:text-white">Grand Total:</span>
            <span class="text-lg font-semibold text-gray-800 dark:text-white" id="grandTotalDisplay">Rp 0</span>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-3 pt-4 mb-4">
        <a href="{{ route('crm.sales-do.index') }}"
            class="px-5 py-2.5 rounded-lg border text-sm font-medium border-gray-300 text-gray-700 dark:text-white dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-white/[0.03]">
            Cancel
        </a>
        <button type="submit"
            class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ $isEdit ? 'Update Sales DO' : 'Create Sales DO' }}
        </button>
    </div>
</div>
@push('scripts')
<script>
    const products = @json($products);
    const existingItems = @json($existingItems);
    const isEdit = {{ $isEdit ? 'true' : 'false' }};
    const taxes = @json($taxes);

    let itemCounter = 0;

    function addItem(product_id = '', qty_ordered = 1, unit_price = 0, discount_percent = 0) {
        const container = document.getElementById('itemsContainer');
        const currentIndex = itemCounter++;
        const rowNumber = container.children.length + 1;

        const row = document.createElement('tr');
        row.className = 'border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800';
        row.setAttribute('data-item-index', currentIndex);

        row.innerHTML = `
            <td class="py-3 px-2 dark:text-gray-400 text-center align-top">${rowNumber}</td>
            <td class="py-3 px-2 align-top">
                <div class="relative" data-product-select="${currentIndex}">
                    <input type="hidden" 
                           name="items[${currentIndex}][product_id]" 
                           id="product_input_${currentIndex}"
                           value="${product_id}"
                           required>
                    
                    <button type="button"
                            onclick="toggleProductSelect(${currentIndex})"
                            class="h-9 w-full flex items-center justify-between rounded-lg border px-3 text-sm
                                   bg-transparent text-gray-800 dark:text-white
                                   border-gray-300 dark:border-gray-700 dark:bg-gray-900
                                   focus:ring-2 focus:ring-blue-500/20">
                        <span id="product_label_${currentIndex}" class="truncate text-left flex-1 text-gray-400">-- Select Product --</span>
                        <svg class="w-4 h-4 text-gray-500 transition-transform flex-shrink-0" id="product_arrow_${currentIndex}"
                             fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-width="1.5" d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>

                    <div id="product_dropdown_${currentIndex}"
                         class="hidden absolute z-[60] mt-1 w-full bg-white dark:bg-gray-900
                                border border-gray-300 dark:border-gray-700
                                rounded-lg shadow-lg max-h-64 overflow-hidden">
                        <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                            <input type="text"
                                   id="product_search_${currentIndex}"
                                   placeholder="Search product..."
                                   onkeyup="searchProducts(${currentIndex})"
                                   class="w-full h-8 rounded border px-3 text-sm
                                          bg-transparent text-gray-800 dark:text-white
                                          border-gray-300 dark:border-gray-700
                                          focus:ring-2 focus:ring-blue-500/20">
                        </div>

                        <div class="max-h-48 overflow-y-auto" id="product_options_${currentIndex}">
                            <button type="button"
                                    onclick="chooseProduct(${currentIndex}, '', '', 0, 0)"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-500
                                           hover:bg-gray-100 dark:hover:bg-gray-800">
                                <em>Clear selection</em>
                            </button>
                            ${products.map(p => `
                                <button type="button"
                                        onclick="chooseProduct(${currentIndex}, '${p.id}', '${(p.name || '').replace(/'/g, "\\'")} (${p.sku || ''})', ${p.stock || 0}, ${p.price || p.unit_price || 0})"
                                        data-product-option="${currentIndex}"
                                        data-product-id="${p.id}"
                                        data-product-label="${(p.name || '')} (${p.sku || ''})"
                                        class="w-full text-left px-4 py-2 text-sm transition
                                               hover:bg-blue-50 dark:hover:bg-gray-800
                                               text-gray-800 dark:text-gray-200">
                                    ${p.name || ''} (${p.sku || ''})
                                </button>
                            `).join('')}
                        </div>
                    </div>
                </div>
            </td>
            <td class="py-3 px-2 align-top">
                <div class="space-y-1">
                    <input type="number"
                           name="items[${currentIndex}][qty_ordered]"
                           id="qty_${currentIndex}"
                           required
                           min="1"
                           value="${qty_ordered}"
                           class="w-full h-9 rounded border border-gray-300 bg-transparent px-2 text-sm text-right dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-300 focus:ring-2 focus:ring-blue-500/10 focus:outline-none"
                           onchange="updateLineTotal(this.closest('tr'))"
                           onkeyup="updateLineTotal(this.closest('tr'))">
                    <div class="text-xs text-gray-500 dark:text-gray-400 text-right" id="stock-info-${currentIndex}">
                        Stock: -
                    </div>
                </div>
            </td>
            <td class="py-3 px-2 align-top">
                <input type="number"
                       name="items[${currentIndex}][unit_price]"
                       id="unit_price_${currentIndex}"
                       required
                       min="0"
                       step="0.01"
                       value="${unit_price}"
                       class="w-full h-9 rounded border border-gray-300 bg-transparent px-2 text-sm text-right dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-300 focus:ring-2 focus:ring-blue-500/10 focus:outline-none"
                       onchange="updateLineTotal(this.closest('tr'))"
                       onkeyup="updateLineTotal(this.closest('tr'))">
            </td>
            <td class="py-3 px-2 align-top">
                <div class="flex items-center gap-1">
                    <input type="number"
                           name="items[${currentIndex}][discount_percent]"
                           id="discount_${currentIndex}"
                           min="0"
                           max="100"
                           step="0.01"
                           value="${discount_percent}"
                           class="w-full h-9 rounded border border-gray-300 bg-transparent px-2 text-sm text-right dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-300 focus:ring-2 focus:ring-blue-500/10 focus:outline-none"
                           onchange="updateLineTotal(this.closest('tr'))"
                           onkeyup="updateLineTotal(this.closest('tr'))">
                    <span class="text-gray-800 dark:text-white text-sm">%</span>
                </div>
            </td>
            <td class="py-3 px-2 text-right text-gray-800 dark:text-white font-medium align-top" data-line-total="0">Rp 0</td>
            <td class="py-3 px-2 text-center align-top">
                <button type="button"
                        onclick="removeItem(this)"
                        class="inline-flex items-center justify-center px-2 py-1 rounded bg-red-600 text-white text-xs hover:bg-red-700 transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        `;

        container.appendChild(row);

        // Update stock info if product is pre-selected
        if (product_id) {
            const product = products.find(p => p.id == product_id);
            if (product) {
                document.getElementById(`product_label_${currentIndex}`).textContent = `${product.name} (${product.sku})`;
                document.getElementById(`product_label_${currentIndex}`).classList.remove('text-gray-400');
                document.getElementById(`product_label_${currentIndex}`).classList.add('text-gray-800', 'dark:text-white');
                updateStockDisplay(currentIndex, product_id);
            }
        }

        // Update line total after adding
        setTimeout(() => updateLineTotal(row), 100);
    }

    function toggleProductSelect(index) {
        const dropdown = document.getElementById(`product_dropdown_${index}`);
        const arrow = document.getElementById(`product_arrow_${index}`);
        const isHidden = dropdown.classList.contains('hidden');
        
        // Close all other dropdowns
        document.querySelectorAll('[id^="product_dropdown_"]').forEach(dd => {
            if (dd.id !== `product_dropdown_${index}`) {
                dd.classList.add('hidden');
            }
        });
        document.querySelectorAll('[id^="product_arrow_"]').forEach(arr => {
            if (arr.id !== `product_arrow_${index}`) {
                arr.classList.remove('rotate-180');
            }
        });

        if (isHidden) {
            dropdown.classList.remove('hidden');
            arrow.classList.add('rotate-180');
            setTimeout(() => {
                document.getElementById(`product_search_${index}`)?.focus();
            }, 100);
        } else {
            dropdown.classList.add('hidden');
            arrow.classList.remove('rotate-180');
        }
    }

    function chooseProduct(index, productId, productLabel, stock = 0, unitPrice = 0) {
        const input = document.getElementById(`product_input_${index}`);
        const label = document.getElementById(`product_label_${index}`);
        const dropdown = document.getElementById(`product_dropdown_${index}`);
        const arrow = document.getElementById(`product_arrow_${index}`);
        const unitPriceInput = document.getElementById(`unit_price_${index}`);

        input.value = productId;
        
        if (productId) {
            label.textContent = productLabel;
            label.classList.remove('text-gray-400');
            label.classList.add('text-gray-800', 'dark:text-white');
            
            // Update unit price
            if (unitPriceInput && unitPrice > 0) {
                unitPriceInput.value = unitPrice;
            }
            
            updateStockDisplay(index, productId);
        } else {
            label.textContent = '-- Select Product --';
            label.classList.add('text-gray-400');
            label.classList.remove('text-gray-800', 'dark:text-white');
            
            // Clear unit price
            if (unitPriceInput) {
                unitPriceInput.value = 0;
            }
            
            updateStockDisplay(index, '');
        }

        dropdown.classList.add('hidden');
        arrow.classList.remove('rotate-180');
        
        // Clear search
        const searchInput = document.getElementById(`product_search_${index}`);
        if (searchInput) {
            searchInput.value = '';
            searchProducts(index);
        }

        // Update line total
        const row = input.closest('tr');
        if (row) updateLineTotal(row);
    }

    function searchProducts(index) {
        const searchInput = document.getElementById(`product_search_${index}`);
        const searchTerm = searchInput.value.toLowerCase();
        const options = document.querySelectorAll(`[data-product-option="${index}"]`);

        options.forEach(option => {
            const label = option.getAttribute('data-product-label').toLowerCase();
            if (label.includes(searchTerm)) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    }

    function updateStockDisplay(itemIndex, productId) {
        const product = products.find(p => p.id == productId);
        const stockInfo = document.getElementById(`stock-info-${itemIndex}`);
        
        if (stockInfo) {
            if (!productId || !product) {
                stockInfo.className = 'text-xs text-gray-500 dark:text-gray-400 text-right';
                stockInfo.textContent = 'Stock: -';
                return;
            }

            const stock = product.stock || 0;
            let stockClass = 'text-xs text-right font-medium ';
            
            if (stock > 10) {
                stockClass += 'text-green-600 dark:text-green-400';
            } else if (stock > 0) {
                stockClass += 'text-yellow-600 dark:text-yellow-400';
            } else {
                stockClass += 'text-red-600 dark:text-red-400';
            }
            
            stockInfo.className = stockClass;
            stockInfo.textContent = `Stock: ${stock}`;
        }
    }

    function removeItem(btn) {
        const row = btn.closest('tr');
        row.remove();

        // Renumber rows
        const rows = document.querySelectorAll('#itemsContainer tr');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });

        updateTotals();
    }

    function updateLineTotal(row) {
        if (!row) return;

        const qtyInput = row.querySelector('input[name*="qty_ordered"]');
        const priceInput = row.querySelector('input[name*="unit_price"]');
        const discountInput = row.querySelector('input[name*="discount_percent"]');
        const lineTotalCell = row.querySelector('[data-line-total]');

        if (!qtyInput || !priceInput || !discountInput || !lineTotalCell) return;

        const qty = parseFloat(qtyInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;

        const lineTotal = (qty * price) * (1 - discount / 100);

        lineTotalCell.textContent = 'Rp ' + lineTotal.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
        lineTotalCell.setAttribute('data-line-total', lineTotal);

        updateTotals();
    }

    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('[data-line-total]').forEach(el => {
            subtotal += parseFloat(el.getAttribute('data-line-total')) || 0;
        });

        const taxSelect = document.querySelector('input[name="tax_id"]');
        const taxId = taxSelect ? taxSelect.value : '';
        const selectedTax = taxes.find(t => t.id == taxId);
        const taxRate = taxId && selectedTax ? parseFloat(selectedTax.rate) : 0;
        const taxAmount = subtotal * (taxRate / 100);
        const grandTotal = subtotal + taxAmount;

        document.getElementById('subtotalDisplay').textContent = 'Rp ' + subtotal.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
        document.getElementById('taxDisplay').textContent = 'Rp ' + taxAmount.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
        document.getElementById('grandTotalDisplay').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('[data-product-select]')) {
            document.querySelectorAll('[id^="product_dropdown_"]').forEach(dd => {
                dd.classList.add('hidden');
            });
            document.querySelectorAll('[id^="product_arrow_"]').forEach(arr => {
                arr.classList.remove('rotate-180');
            });
        }
    });

    // Event Listeners
    document.getElementById('addItemBtn').addEventListener('click', () => addItem());

    // Watch for tax changes
    let taxObserverTimeout;
    function observeTaxChanges() {
        clearTimeout(taxObserverTimeout);
        taxObserverTimeout = setTimeout(() => {
            const taxHiddenInput = document.querySelector('input[name="tax_id"]');
            if (taxHiddenInput) {
                const observer = new MutationObserver(() => {
                    updateTotals();
                });
                observer.observe(taxHiddenInput, {
                    attributes: true,
                    attributeFilter: ['value']
                });

                taxHiddenInput.addEventListener('change', updateTotals);
                taxHiddenInput.addEventListener('input', updateTotals);
            } else {
                if (document.readyState !== 'complete') {
                    observeTaxChanges();
                }
            }
        }, 300);
    }

    // Initialize items on page load
    document.addEventListener('DOMContentLoaded', function() {
        observeTaxChanges();

        if (existingItems && existingItems.length > 0) {
            existingItems.forEach(item => {
                addItem(
                    item.product_id || '',
                    item.qty_ordered || 1,
                    item.unit_price || 0,
                    item.discount_percent || 0
                );
            });
        } else {
            addItem();
        }

        setTimeout(() => {
            updateTotals();
        }, 500);
    });

    let initCheckCount = 0;
    const initCheckInterval = setInterval(() => {
        updateTotals();
        initCheckCount++;
        if (initCheckCount > 10) {
            clearInterval(initCheckInterval);
        }
    }, 500);


</script>
@endpush