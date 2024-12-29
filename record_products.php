<?php
session_start();
if (!isset($_POST['search'])) {
    include 'include/header.php';
}
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>บันทึกข้อมูลสินค้า</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 0px 20px 10px 20px;
    }

    ::-webkit-scrollbar {
        display: none;
    }

    .container {
        max-width: 80%;
        margin: auto;
        background: #fff;
        padding: 0.2rem 2.5rem 0.2rem 2.5rem;
        border-radius: 8px;
        box-shadow: 0px 0px 10px 5px rgba(0, 0, 0, 0.1);
        margin-top: 6.5rem;
    }

    h2 {
        text-align: center;
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
        font-weight: bold;
    }


    .button-group {
        display: flex;
        justify-content: flex-start;
        gap: 20px;
        margin-bottom: 20px;
        width: 100%;
    }

    .button-group button {
        padding: 10px 20px;
        font-size: 14px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        color: white;
        outline: none;
    }

    .button-group button i {
        font-size: 20px;
        margin-right: 5px;
    }

    .button-group .delete-button {
        background-color: #dc3545;
        display: flex;
        align-items: center;
    }

    .button-group .add-button {
        background-color: #28a745;
        display: flex;
        align-items: center;
    }

    .button-group .edit-button {
        background-color: #ffc107;
        display: flex;
        align-items: center;
    }

    .import-button {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-left: auto;
    }

    .import-button button {
        padding: 0px;
        border: none;
        cursor: pointer;
        margin: 0px;
        color: black;
    }

    .import-button .import-icon {
        background-color: #007bff;
        color: white;
    }

    .import-button .import-text {
        background-color: #28a745;
        color: white;
    }

    .form-container h3 {
        width: 100%;
        margin-bottom: 1rem;
        text-align: center;
        font-weight: bold;
    }

    .form-container .submit-button {
        text-align: center;
        width: 100%;
    }

    .form-container .submit-button button {
        margin-right: 1rem;
    }

    .form-container {
        background: #cfd8e5;
        padding: 2rem 2rem 1rem 2rem;
        border-radius: 10px;
        margin: 1.5rem 0 1.3rem 0;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
        justify-content: center;
    }

    .form-row {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .form-row label {
        margin-bottom: 5px;
        font-weight: bold;
        color: #003d99;
        width: 90%;
    }

    .form-row input {
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        flex-shrink: 1;
        width: 90%;
    }

    .form-row select {
        padding: 13px;
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        flex-shrink: 1;
        width: 90%;
    }

    .submit-button {
        grid-column: span 3;
        display: flex;
        justify-content: center;
        margin-top: 1rem;
    }


    .modal-header .modal-title {
        margin: 0;
        font-size: 1.5rem;
        text-align: center;
    }

    .modal-body {
        text-align: center;

        p {
            font-size: 16px;
            margin: 0;
        }
    }

    .modal-footer {
        justify-content: center;

        button {
            width: 25%;
        }
    }

    #check-before {
        margin-left: auto;
    }

    #check-before div {
        margin-left: 15px;
    }

    ul {
        list-style: none;
        padding-left: 15px;
        margin-left: 0;
        margin-top: 10px;
        margin-bottom: 10px;
    }

    .delete-row,
    .warning-row {
        width: 50%;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>ข้อมูลสินค้า</h2>
        <div class="button-group">
            <button id="delete-Button" class="delete-button">
                <i class="fa-solid fa-trash"></i>ลบข้อมูล
            </button>
            <!-- <button class="add-button">
                <i class="fa-solid fa-circle-plus"></i>เพิ่ม
            </button> -->
            <div class="import-button">
                <input type="file" id="uploadExcel" accept=".xlsx, .xls" class="d-none">
                <button id="uploadButton" class="btn btn-link">
                    <i class="fa-regular fa-file-excel"></i><br>
                    นำเข้าข้อมูลสินค้า
                </button>
            </div>
            <button id="edit-Button" class="edit-button">
                <i class="fa-regular fa-pen-to-square"></i>แก้ไขข้อมูล
            </button>
        </div>
        <form class="form-container" id="productForm">
            <div class="form-row">
                <label>รหัสสินค้า</label>
                <input type="text" id="product_code" name="product_code" required>
            </div>
            <div class="form-row">
                <label>ชื่อสินค้า</label>
                <input type="text" id="product_name" name="product_name" required>
            </div>
            <div class="form-row">
                <label for="product_model">รุ่นการผลิต</label>
                <input type="text" id="product_model" name="product_model" required>
            </div>
            <div class="form-row">
                <label for="production_date">วันผลิต</label>
                <input type="date" id="production_date" name="production_date" required>
            </div>
            <div class="form-row">
                <label for="shelf_life">อายุสินค้า(วัน)</label>
                <input type="number" id="shelf_life" name="shelf_life" required>
            </div>
            <div class="form-row">
                <label for="expiration_date">วันหมดอายุ</label>
                <input type="date" id="expiration_date" name="expiration_date" required>
            </div>
            <div class="form-row">
                <label for="sticker_color">สีสติ๊กเกอร์</label>
                <select id="sticker_color" name="sticker_color" required>
                    <option value="" disabled selected>เลือกสีสติ๊กเกอร์</option>
                    <option value="หมดอายุเดือน 1" style="background-color: #E3D200; color: #000;">หมดอายุเดือน 1
                    </option>
                    <option value="หมดอายุเดือน 2" style="background-color: #00C4B4; color: #fff;">หมดอายุเดือน 2
                    </option>
                    <option value="หมดอายุเดือน 3" style="background-color: #EE700E; color: #fff;">หมดอายุเดือน 3
                    </option>
                    <option value="หมดอายุเดือน 4" style="background-color: #EA12B1; color: #fff;">หมดอายุเดือน 4
                    </option>
                    <option value="หมดอายุเดือน 5" style="background-color: #C8E9F0; color: #000;">หมดอายุเดือน 5
                    </option>
                    <option value="หมดอายุเดือน 6" style="background-color: #02A737; color: #fff;">หมดอายุเดือน 6
                    </option>
                    <option value="หมดอายุเดือน 7" style="background-color: #EAEAA2; color: #000;">หมดอายุเดือน 7
                    </option>
                    <option value="หมดอายุเดือน 8" style="background-color: #00A1CD; color: #fff;">หมดอายุเดือน 8
                    </option>
                    <option value="หมดอายุเดือน 9" style="background-color: #AA7964; color: #fff;">หมดอายุเดือน 9
                    </option>
                    <option value="หมดอายุเดือน 10" style="background-color: #F4D3DC; color: #000;">หมดอายุเดือน 10
                    </option>
                    <option value="หมดอายุเดือน 11" style="background-color: #B9F4A2; color: #000;">หมดอายุเดือน 11
                    </option>
                    <option value="หมดอายุเดือน 12"
                        style="background-color: #FFFFFF; color: #000; border: 1px solid #ccc;">หมดอายุเดือน 12
                    </option>
                    <option value="ไม่มีวันหมดอายุ" style="background-color: #999999; color: #fff;">ไม่มีวันหมดอายุ
                    </option>
                </select>
            </div>
            <div class="form-row">
                <label for="reminder_date">เตือนล่วงหน้า</label>
                <input type="date" id="reminder_date" name="reminder_date" required disabled>
            </div>
            <div class="form-row">
                <label for="received_date">วันรับเข้า</label>
                <input type="date" id="received_date" name="received_date" required>
            </div>
            <div class="form-row">
                <label for="quantity">จำนวน</label>
                <input type="number" id="quantity" name="quantity" required>
            </div>
            <div class="form-row">
                <label for="unit">หน่วย</label>
                <input type="text" id="unit" name="unit" required>
            </div>
            <div class="form-row">
                <label for="unit_cost">ราคาทุนต่อหน่วย</label>
                <input type="number" id="unit_cost" name="unit_cost" required>
            </div>
            <div class="form-row">
                <label for="sender_code">รหัสผู้ส่ง</label>
                <input type="text" id="sender_code" name="sender_code" required>
            </div>
            <div class="form-row">
                <label for="sender_company">ชื่อบริษัทผู้ส่ง</label>
                <input type="text" id="sender_company" name="sender_company" required>
            </div>
            <div class="form-row">
                <label for="recorder">ผู้บันทึกข้อมูล</label>
                <input type="text" id="recorder" name="recorder" required>
            </div>
            <div class="form-row">
                <label for="unit_price">ราคาขายต่อหน่วย(บาท)</label>
                <input type="number" id="unit_price" name="unit_price" required>
            </div>
            <div class="form-row">
                <label for="category">หมวดหมู่สินค้า</label>
                <input type="text" id="category" name="category" required>
            </div>
            <div class="form-row">
                <label for="position">ตำแหน่งสินค้า</label>
                <input type="text" id="position" name="position" required>
            </div>
            <div class="submit-button">
                <button type="button" class="btn btn-primary" id="confirmButton">บันทึกข้อมูล</button>
                <button type="reset" class="btn btn-secondary" id="resetButton">ล้างข้อมูล</button>
                <button type="button" class="btn btn-success" id="saveButton">ยืนยัน</button>
                <button type="button" class="btn btn-warning" id="editButton">แก้ไข</button>
                <button type="button" class="btn btn-danger" id="cancelButton">ยกเลิก</button>
            </div>
        </form>

        <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProductModalLabel">เลือกสินค้าที่ต้องการแก้ไข</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="searchEditProduct" class="form-control mb-3"
                            placeholder="ค้นหาโดยใช้รหัสสินค้า หรือ ชื่อสินค้า...">
                        <table class="table table-bordered" id="editProductTable" style="display: none;">
                            <thead>
                                <tr>
                                    <th>รหัสสินค้า</th>
                                    <th>ชื่อสินค้า</th>
                                    <th>จำนวน</th>
                                    <th>เลือก</th>
                                </tr>
                            </thead>
                            <tbody id="editProductTableBody">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteProductModalLabel">เลือกสินค้าที่ต้องการลบ</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="searchDeleteProduct" class="form-control mb-3"
                            placeholder="ค้นหาโดยใช้รหัสสินค้า หรือ ชื่อสินค้า...">
                        <table class="table table-bordered" id="deleteProductTable" style="display: none;">
                            <thead>
                                <tr>
                                    <th>รหัสสินค้า</th>
                                    <th>ชื่อสินค้า</th>
                                    <th>จำนวน</th>
                                    <th>ลบ</th>
                                </tr>
                            </thead>
                            <tbody id="deleteProductTableBody">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {

        function formatDateToInput(dateString) {
            console.log('date', typeof dateString)
            if (!dateString) return "";
            const date = new Date(dateString);
            if (isNaN(date)) return "";
            return date.toISOString().split("T")[0];
        }

        function resetFormToInitialState() {
            confirmButton.style.display = "inline-block";
            resetButton.style.display = "inline-block";
            saveButton.style.display = "none";
            editButton.style.display = "none";
            cancelButton.style.display = "none";
            confirmButton.disabled = true;
            resetButton.disabled = true;

            const spans = document.querySelectorAll("#productForm .form-row span");
            spans.forEach((span) => {
                const parent = span.parentElement;
                const input = parent.querySelector("input, select");

                if (input) {
                    span.style.display = "none";
                    input.style.display = "block";
                }
            });

            const form = document.getElementById("productForm");
            form.reset();
        }

        $('#editProductModal').on('hidden.bs.modal', function() {
            document.getElementById('searchEditProduct').value = '';
            document.getElementById('editProductTable').style.display = 'none';
            document.getElementById('editProductTableBody').innerHTML = '';
        });

        $('#deleteProductModal').on('hidden.bs.modal', function() {
            document.getElementById('searchDeleteProduct').value = '';
            document.getElementById('deleteProductTable').style.display = 'none';
            document.getElementById('deleteProductTableBody').innerHTML = '';
        });


        const deleteButton = document.getElementById("delete-Button");
        const deleteProductModal = new bootstrap.Modal(document.getElementById("deleteProductModal"));

        deleteButton.addEventListener("click", function() {
            deleteProductModal.show();
        });

        const deleteProductTable = document.getElementById("deleteProductTable");
        const searchInput = document.getElementById("searchDeleteProduct");
        const tableBody = document.getElementById("deleteProductTableBody");

        searchInput.addEventListener("input", function() {
            const query = searchInput.value.trim();

            if (query === "") {
                deleteProductTable.style.display = "none";
                tableBody.innerHTML = "";
                return;
            }

            // ส่งคำขอ AJAX ไปยัง fetch_products.php
            fetch(`fetch_products.php?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    tableBody.innerHTML = "";

                    if (data.length > 0) {
                        data.forEach(product => {
                            const row = document.createElement("tr");
                            row.innerHTML = `
                            <td>${product.product_code}</td>
                            <td>${product.product_name}</td>
                            <td>${product.quantity}</td>
                            <td><button class="btn btn-danger btn-sm delete-row">ลบ</button></td>
                        `;
                            tableBody.appendChild(row);
                        });
                        deleteProductTable.style.display = "table";
                    } else {
                        const noDataRow = document.createElement("tr");
                        noDataRow.innerHTML = `
                    <td colspan="4" class="text-center">ไม่พบสินค้า</td>
                `;
                        tableBody.appendChild(noDataRow);
                        deleteProductTable.style.display = "table";
                    }
                })
                .catch(error => {
                    console.error("Error fetching data:", error);
                });
        });

        tableBody.addEventListener("click", function(event) {
            if (event.target.classList.contains("delete-row")) {
                const row = event.target.closest("tr");
                const productCode = row.querySelector("td").textContent;

                if (confirm(`คุณต้องการลบสินค้า ${productCode} หรือไม่?`)) {
                    fetch("delete_product.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                product_code: productCode
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert("ลบสินค้าสำเร็จ!");
                                row.remove();
                            } else {
                                alert("เกิดข้อผิดพลาดในการลบสินค้า");
                            }
                        })
                        .catch(error => {
                            console.error("Error deleting product:", error);
                        });
                }
            }
        });

        const edit_Button = document.getElementById("edit-Button");
        const editProductModal = new bootstrap.Modal(document.getElementById("editProductModal"));

        edit_Button.addEventListener("click", function() {
            editProductModal.show();
        });

        const editProductTable = document.getElementById("editProductTable");
        const searchEditInput = document.getElementById("searchEditProduct");
        const edittableBody = document.getElementById("editProductTableBody");

        searchEditInput.addEventListener("input", function() {
            const query = searchEditInput.value.trim();

            if (query === "") {
                editProductTable.style.display = "none";
                edittableBody.innerHTML = "";
                return;
            }

            // ส่งคำขอ AJAX ไปยัง fetch_products.php
            fetch(`fetch_products.php?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    edittableBody.innerHTML = "";

                    if (data.length > 0) {
                        data.forEach(product => {
                            const row = document.createElement("tr");
                            row.innerHTML =
                                `
                                <td>${product.product_code}</td>
                            <td>${product.product_name}</td>
                            <td>${product.quantity}</td>
                            <input type="hidden" name="product_model" value="${product.product_model}">
                            <input type="hidden" name="production_date" value="${product.production_date}">
                            <input type="hidden" name="shelf_life" value="${product.shelf_life}">
                            <input type="hidden" name="expiration_date" value="${product.expiration_date}">
                            <input type="hidden" name="sticker_color" value="${product.sticker_color}">
                            <input type="hidden" name="reminder_date" value="${product.reminder_date}">
                            <input type="hidden" name="received_date" value="${product.received_date}">
                            <input type="hidden" name="unit" value="${product.unit}">
                            <input type="hidden" name="unit_cost" value="${product.unit_cost}">
                            <input type="hidden" name="unit_price" value="${product.unit_price}">
                            <input type="hidden" name="sender_code" value="${product.sender_code}">
                            <input type="hidden" name="sender_company" value="${product.sender_company}">
                            <input type="hidden" name="recorder" value="${product.recorder}">
                            <input type="hidden" name="category" value="${product.category}">
                            <input type="hidden" name="status" value="${product.status}">
                            <input type="hidden" name="position" value="${product.position}">
                            <td><button class="btn btn-warning btn-sm warning-row" data-code="${product.product_code}">แก้ไข</button></td>`;
                            edittableBody.appendChild(row);
                        });
                        editProductTable.style.display = "table";
                    } else {
                        const noDataRow = document.createElement("tr");
                        noDataRow.innerHTML =
                            `<td colspan="4" class="text-center">ไม่พบสินค้า</td>`;
                        edittableBody.appendChild(noDataRow);
                        editProductTable.style.display = "table";
                    }
                })
                .catch(error => {
                    console.error("Error fetching data:", error);
                });
        });

        edittableBody.addEventListener("click", function(event) {
            if (event.target.classList.contains("warning-row")) {
                const row = event.target.closest("tr");
                const productCode = row.children[0].textContent;
                const productName = row.children[1].textContent;
                const quantity = row.children[2].textContent;

                const hiddenInputs = row.querySelectorAll('input[type="hidden"]');
                const hiddenData = {};

                hiddenInputs.forEach(input => {
                    hiddenData[input.name] = input.value.trim();
                });

                document.getElementById("product_code").value = productCode;
                document.getElementById("product_name").value = productName;
                document.getElementById("quantity").value = quantity;

                Object.keys(hiddenData).forEach(key => {
                    const inputField = document.getElementById(key);
                    if (inputField && inputField.type === "date") {
                        inputField.value = formatDateToInput(hiddenData[
                        key]); 
                    } else if (inputField) {
                        inputField.value = hiddenData[key];
                    }
                });

                const formElements = document.querySelectorAll(
                    "#productForm .form-row input, #productForm .form-row select");
                formElements.forEach(element => {
                    if (element.hasAttribute("disabled")) {
                        element.disabled = true;
                    } else {
                        element.disabled = false;
                    }
                });

                confirmButton.disabled = false;
                resetButton.disabled = false;
                editProductModal.hide();
            }
        });

        document.getElementById("saveButton").addEventListener("click", function(event) {
            event.preventDefault();

            const form = document.getElementById("productForm");
            const formData = new FormData(form);
            const productData = {};
            formData.forEach((value, key) => {
                productData[key] = value;
            });

            fetch("save_product.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(productData),
                })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok");
                    }
                    return response.json();
                })
                .then((data) => {
                    if (data.success) {
                        if (data.exists) {
                            alert("แก้ไขสำเร็จแล้ว!");
                        } else {
                            alert("บันทึกสำเร็จแล้ว!");
                        }
                        resetFormToInitialState();
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("เกิดข้อผิดพลาดในการเชื่อมต่อ");
                });
        });


        document.getElementById("confirmButton").addEventListener("click", function(event) {
            event.preventDefault();

            const formElements = document.querySelectorAll(
                "#productForm .form-row input, #productForm .form-row select");

            formElements.forEach((element) => {
                const parent = element.parentElement;
                if (element.disabled) {
                    const span = document.createElement("span");
                    span.textContent = element.value;
                    span.style.display = "block";
                    span.style.padding = "9px";
                    span.style.marginBottom = "10px";
                    span.style.border = "1px solid #ccc";
                    span.style.borderRadius = "5px";
                    span.style.backgroundColor = "#f9f9f9";
                    span.style.cursor = "default";
                    span.style.width = "90%";
                    span.style.display = "none";
                    parent.insertBefore(span, element);
                } else {
                    const span = document.createElement("span");
                    span.textContent = element.value;
                    span.style.display = "block";
                    span.style.padding = "10px";
                    span.style.marginBottom = "10px";
                    span.style.border = "1px solid #ccc";
                    span.style.borderRadius = "5px";
                    span.style.backgroundColor = "#EFEFEF4D";
                    span.style.cursor = 'default';
                    span.style.width = '90%';

                    element.style.display = "none";
                    parent.appendChild(span);
                }
            });
        });

        document.getElementById("editButton").addEventListener("click", function(event) {
            event.preventDefault();
            const spans = document.querySelectorAll("#productForm .form-row span");
            spans.forEach((span) => {
                const parent = span.parentElement;
                const input = parent.querySelector("input, select");

                if (input) {
                    span.style.display = "none";
                    input.style.display = "block";
                }
            });
        });

        document.getElementById("cancelButton").addEventListener("click", function(event) {

            event.preventDefault();
            const spans = document.querySelectorAll("#productForm .form-row span");
            spans.forEach((span) => {
                const parent = span.parentElement;
                const input = parent.querySelector("input, select");

                if (input) {
                    span.style.display = "none";
                    input.style.display = "block";
                }
            });

            const form = document.getElementById("productForm");
            form.reset();
        });

        const confirmButton = document.getElementById("confirmButton");
        const resetButton = document.getElementById("resetButton");
        const saveButton = document.getElementById("saveButton");
        const editButton = document.getElementById("editButton");
        const cancelButton = document.getElementById("cancelButton");
        const formElements = document.querySelectorAll(
            "#productForm .form-row input, #productForm .form-row select");

        confirmButton.disabled = true;
        resetButton.disabled = true;
        saveButton.style.display = "none";
        editButton.style.display = "none";
        cancelButton.style.display = "none";

        const checkFormCompletion = () => {
            let allFilled = true;
            formElements.forEach((element) => {
                if (!element.disabled && element.value.trim() === "") {
                    allFilled = false;
                }
            });
            confirmButton.disabled = !allFilled;
        };

        formElements.forEach((element) => {
            element.addEventListener("input", () => {
                resetButton.disabled = false;
                checkFormCompletion();
            });
        });

        confirmButton.addEventListener("click", () => {
            confirmButton.style.display = "none";
            resetButton.style.display = "none";
            saveButton.style.display = "inline-block";
            editButton.style.display = "inline-block";
            cancelButton.style.display = "inline-block";
        });

        resetButton.addEventListener("click", () => {
            confirmButton.disabled = true;
            resetButton.disabled = true;

            const form = document.getElementById("productForm");
            form.reset();
        });

        editButton.addEventListener("click", () => {
            confirmButton.style.display = "inline-block";
            resetButton.style.display = "inline-block";
            saveButton.style.display = "none";
            editButton.style.display = "none";
            cancelButton.style.display = "none";
        });

        cancelButton.addEventListener("click", () => {
            confirmButton.style.display = "inline-block";
            resetButton.style.display = "inline-block";
            saveButton.style.display = "none";
            editButton.style.display = "none";
            cancelButton.style.display = "none";
            confirmButton.disabled = true;
            resetButton.disabled = true;
        });
    });

    document.getElementById('uploadButton').addEventListener('click', function() {
        document.getElementById('uploadExcel').click();
    });

    document.getElementById('uploadExcel').addEventListener('change', function(event) {
        let file = event.target.files[0];
        if (file) {
            importExcel();
        } else {
            alert('กรุณาเลือกไฟล์ Excel');
        }
    });

    function importExcel() {
        let fileInput = document.getElementById('uploadExcel');
        let file = fileInput.files[0];

        if (!file) {
            alert('กรุณาเลือกไฟล์ Excel ก่อน');
            return;
        }

        let reader = new FileReader();

        reader.onload = function(event) {
            let data = new Uint8Array(event.target.result);
            let workbook = XLSX.read(data, {
                type: 'array'
            });
            let firstSheetName = workbook.SheetNames[0];
            let worksheet = workbook.Sheets[firstSheetName];

            let jsonData = XLSX.utils.sheet_to_json(worksheet, {
                header: 1
            });

            postExcelDataToDatabase(jsonData);
        };

        reader.readAsArrayBuffer(file);
    }

    function formatDateToISO(dateString) {
    console.log('Original date:', dateString);

    if (!dateString) {
        console.warn('Invalid date string:', dateString);
        return null;
    }

    // ตรวจสอบว่า dateString เป็นตัวเลขหรือไม่ (Excel serial date)
    if (!isNaN(dateString)) {
        // Excel serial date เริ่มต้นที่วันที่ 1 มกราคม 1900 (ลบ 1 วันเพื่อชดเชย bug Excel)
        const excelEpoch = new Date(1899, 11, 30);
        const date = new Date(excelEpoch.getTime() + dateString * 86400000); // 86400000 ms ในหนึ่งวัน
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`; // รูปแบบ ISO (yyyy-mm-dd)
    }

    // ถ้าไม่ใช่ Excel serial date ให้แปลงในรูปแบบ dd-mm-yyyy
    const parts = dateString.split('-');
    if (parts.length === 3) {
        const day = parts[0].padStart(2, '0');
        const month = parts[1].padStart(2, '0');
        const year = parts[2].length === 2 ? '20' + parts[2] : parts[2];
        return `${year}-${month}-${day}`;
    }

    console.warn('Invalid date format:', dateString);
    return null;
}



    function postExcelDataToDatabase(data) {
        if (data.length < 2) {
            alert('ไฟล์ไม่มีข้อมูลที่สามารถบันทึกได้');
            return;
        }

        function checkDuplicate(product_code) {
            return fetch(`import_excel.php?action=check_duplicate&product_code=${encodeURIComponent(product_code)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(text => {
                    try {
                        return JSON.parse(text).exists;
                    } catch (error) {
                        console.error('Error parsing JSON:', error, 'Response Text:', text);
                        throw error;
                    }
                });
        }

        data.slice(1).forEach((row, index) => {
            let product_code = row[0] || '';

            if (!product_code) {
                alert(`แถวที่ ${index + 2} ไม่มี รหัสสินค้า`);
                return;
            }

            checkDuplicate(product_code).then(isDuplicate => {
                if (isDuplicate) {
                    alert(`ข้อมูลซ้ำ: รหัสสินค้า "${product_code}" มีข้อมูลแล้ว`);
                } else {
                    let formData = new FormData();
                    formData.append('save', 'true');
                    formData.append('product_code', product_code);
                    formData.append('product_name', row[1] || '');
                    formData.append('product_model', row[2] || '');
                    formData.append('production_date', row[3] ? formatDateToISO(row[3]) :
                        ''); // วันที่ผลิต
                    formData.append('shelf_life', row[4] || '');
                    formData.append('expiration_date', row[5] ? formatDateToISO(row[5]) :
                        ''); // วันหมดอายุ
                    formData.append('sticker_color', row[6] || '');
                    formData.append('reminder_date', row[7] ? formatDateToISO(row[7]) :
                        ''); // เตือนล่วงหน้า
                    formData.append('received_date', row[8] ? formatDateToISO(row[8]) :
                        ''); // วันรับเข้า
                    formData.append('quantity', row[9] || '');
                    formData.append('unit', row[10] || '');
                    formData.append('unit_cost', row[11] || '');
                    formData.append('sender_code', row[12] || '');
                    formData.append('sender_company', row[13] || '');
                    formData.append('recorder', row[14] || '');
                    formData.append('unit_price', row[15] || '');
                    formData.append('category', row[16] || '');
                    formData.append('position', row[17] || '');

                    fetch('import_excel.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.text())
                        .then(result => {
                            alert(`บันทึกสำเร็จ: รหัสสินค้า "${product_code}"`);
                        })
                        .catch(error => {
                            console.error(`เกิดข้อผิดพลาดกับ รหัสสินค้า "${product_code}":`, error);
                        });
                }
            });
        });
    }
    </script>
</body>

</html>