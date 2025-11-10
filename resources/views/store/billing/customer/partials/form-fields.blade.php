            <div class="form-row mb-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customer_phone">Customer Phone Number</label>
                        <div class="form-group d-flex align-items-center">
                            <input type="text" list="customer_phones" name="customer_phone" id="customer_phone" class="form-control" placeholder="Enter or choose customer phone number...">
                            <datalist id="customer_phones">
                            </datalist>
                            <button type="button" class="btn btn-sm btn-primary mx-3" data-toggle="modal"
                                data-target="#addCustomer">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="doctor_name">Doctor Name</label>
                        <div class="form-group d-flex align-items-center">
                            <select data-enable-search="true" name="doctor_name[]" id="doctor_name"
                                class="form-control">
                                <option value="">Choose Doctor Name...</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-primary mx-3" data-toggle="modal"
                                data-target="#addDoctor">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row mb-2">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="customer_name">Customer Name</label>
                        <div class="form-group d-flex align-items-center">
                            <input type="text" name="customer_name" id="customer_name" class="form-control" disabled>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="paymentType">Payment Type</label>
                        <div class="form-group d-flex align-items-center">
                            <select data-enable-search="true" name="paymentType[]" id="paymentType"
                                class="form-control">
                                <option value="">Choose Payment Type...</option>
                                <option value="online">Online</option>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="invoiceNo">Invoice No</label>
                        <div class="form-group d-flex align-items-center">
                            <input type="text" name="invoiceNo" id="invoiceNo" class="form-control"
                                value="{{ uniqid() }}" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dynamicForm">
                    <thead>
                        <tr class="table">
                            <th>Product</th>
                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>Pack</th>
                            <th>Pack Size</th>

                            <th>Remaining Qty</th>
                            <th>Unit Value</th>
                            <th>Qty</th>
                            <th>Discount</th>
                            <th>Total Amount</th>
                            <th>GST Rate (%)</th>
                            <th>GST Amount</th>
                            <th>CGST</th>
                            <th>SGST</th>

                        </tr>
                    </thead>
                    <tbody id="formBody">

                    </tbody>
                </table>
            </div>

            <button type="button" name="add_row" id="add_row"
                class="btn btn-sm btn-secondary mb-3  mt-3 float-right ml-3">
                Add New Row
            </button>

            <div class="form-group text-right mt-3 mx-4 row d-flex justify-content-end">
                <div class="col-md-2">
                    <label for="totalAmount">Total Amount: </label>
                    <span id="totalAmount">0</span>
                </div>
                <div class="col-md-2">
                    <label for="totalGST">GST: </label>
                    <span id="totalGST">0</span>
                </div>
                <div class="col-md-2">
                    <label for="totalCGST">CGST: </label>
                    <span id="totalCGST">0</span>
                </div>
                <div class="col-md-2">
                    <label for="totalSGST">SGST: </label>
                    <span id="totalSGST">0</span>
                </div>
            </div>

            <div class="form-group text-right">
                <button type="button" name="submitBilling" id="submitBilling" class="btn btn-primary">Submit</button>
            </div>