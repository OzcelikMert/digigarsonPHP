let pages,basket;
$( document ).ready(function() {
	pages = new pop_pages();
	let _panel = new page_panel();
	pages.add("basket","#page_basket");
	pages.add("address_list","#page_address_list");
	pages.add("address_edit","#page_edit_address");
	pages.add("qrcode_scan","#page_qrcode_scan");
	pages.add("search","#page_search");
	pages.add("other","#page_other");
	pages.add("company_details","#page_company_details");
	pages.add("product_list","#page_product_list");
	pages.add("product_details","#page_product_details");
	pages.add("order_confirm","#page_order_confirm");
	pages.add("order_history","#page_order_histroy");
	pages.add("login_and_register","#login_and_register");

	$("[open_page]").on("click",function (){pages.open($(this).attr('open_page'))});
	$("[close_page]").on("click",function (){ pages.close($(this).attr('close_page'))});
	$("[all_close]").on("click",function (){ pages.close_all("company_details")});
});


let page_panel = (function() {
	let default_ajax_path = `${settings.paths.primary.PHP}panel/`;
	let is_open = true;
	let set_type = {
		NOTIFICATION: 13
	}

	function page_panel(){
		company_details.initialize();
		page_panel.notification.initialize();
	}

	//Sections
	let company_details = {
		id_list: {
			COMPANY_DETAILS: "#page_company_details",
			ORDER_CONFIRM: "#page_order_confirm",
			PRODUCT_DETAILS: "#page_product_details",
			BASKET: "#page_basket",
			NAVBAR_ADD_BASKET: "#navbar_add_basket",
			PRODUCT_ADD_TO_BASKET: "#product_add_to_basket",
		},
		class_list: {
			MATERIALS_BTN: ".e_materials_btn",
			GET_CATEGORIES: ".e_get_categories",
			GET_PRODUCTS: ".e_get_products",
			SELECT_LANGUAGE: ".e_select_language",
			COMPANY_MENU: ".company.menu",
			NOTIFICATION_SHOW_BTN :".e_notification_show"
		},
		set_type: {
			GET_BRANCH: 2,
			SET_ORDER: 10,
			GET_ORDERS: 11,
			LANGUAGE: 12
		},
		type: {
			ALL: 1
		},
		element_type: {
			CATEGORY: 1,
			PRODUCT: 2,
		},
		option_types:{
			MATERIALS: 1,
			SINGLE_SELECT: 2,
			MULTI_SELECT: 3,
		},
		variable_list: {
			BASKET: Array(),
			SELECTED_PRODUCT_ID: null,
			SELECTED_PRODUCT_NAME: null,
			SELECTED_PRODUCT_PRICE: 0,
			SELECTED_PRODUCT_OPTION_PRICE: 0,
			SELECTED_PRODUCT_TOTAL_PRICE: 0,
			SELECTED_PRODUCT_AMOUNT: 1,
			SELECTED_PRODUCT_IMAGE: "",
			SEND_TAKEAWAY: {
				TOTAL_PRICE: 0,
				PAYMENT_SELECT: false,
				ADDRESS_SELECT: false,
				PAYMENT_ID: null,
				USER_ADDRESS_ID: null,
			},
		},
		branch_keys :{
			BRANCH_ID: "id",
			ADDRESS: "address",
			CLOSE_TIME: "close_time",
			CURRENCY_ID: "currency_id",
			LOGO: "logo",
			NAME: "name",
			ONLINE_PAYMENT: "online_payment",
			OPEN_TIME: "open_time",
			QR_ACTIVE: "qr_active",
			QR_DISCOUNT: "qr_discount",
			TAKE_AWAY_ACTIVE: "take_away_active",
			TAKE_AWAY_AMOUNT: "take_away_amount",
			TAKE_AWAY_TIME: "take_away_time",
			TYPE: "type",
			TYPE_ID: "type_id",
		},
		get :function(send= true, data){
			let self = this;
			if (send){
				let params = new URLSearchParams(new URL(window.location.href).search)
				let url = params.get('url');
				let data = {
					set_type : self.set_type.GET_BRANCH,
					type : self.type.ALL,
					url : url
				};
				self.set(data,null,false);
			}

			else {
				helper.log(data)
				//let user_data = main.data_list.USER.INFO;
				if(variable.isset(()=> data.rows.BRANCH)) main.data_list.BRANCH.INFO = data.rows.BRANCH[0];
				if(variable.isset(()=> data.rows.PRODUCTS)) main.data_list.PRODUCTS = data.rows.PRODUCTS;
				if(variable.isset(()=> data.rows.PRODUCT_LINKED_OPTIONS)) main.data_list.PRODUCT_LINKED_OPTIONS = data.rows.PRODUCT_LINKED_OPTIONS;
				if(variable.isset(()=> data.rows.OPTION_TYPES)) main.data_list.OPTION_TYPES = data.rows.OPTION_TYPES;
				if(variable.isset(()=> data.rows.PRODUCT_OPTIONS)) main.data_list.PRODUCT_OPTIONS = data.rows.PRODUCT_OPTIONS;
				if(variable.isset(()=> data.rows.PRODUCT_OPTION_ITEMS)) main.data_list.PRODUCT_OPTION_ITEMS = data.rows.PRODUCT_OPTION_ITEMS;
				if(variable.isset(()=> data.rows.CATEGORIES)) main.data_list.CATEGORIES = array_list.sort( data.rows.CATEGORIES,"rank")
				if(variable.isset(()=> data.rows.ACCEPTED_ADDRESS)) main.data_list.ACCEPTED_ADDRESS = data.rows.ACCEPTED_ADDRESS;
				if(variable.isset(()=> data.rows.WORK_TIMES)) main.data_list.BRANCH.WORK_TIMES = data.rows.WORK_TIMES;
				if(variable.isset(()=> data.rows.BRANCH_PAYMENT_TYPES)) main.data_list.BRANCH.PAYMENT_TYPES = data.rows.BRANCH_PAYMENT_TYPES;
				if(variable.isset(()=> data.rows.PAYMENT_TYPES)) main.data_list.PAYMENT_TYPES = data.rows.PAYMENT_TYPES;
				if(variable.isset(()=> data.rows.NOTIFICATIONS)) main.data_list.NOTIFICATIONS = data.rows.NOTIFICATIONS;
				if(variable.isset(()=> data.rows.USER_INFO)) {
					main.data_list.USER.INFO = data.rows.USER_INFO;
					new page_address();
				}

				let branch_id = main.data_list.BRANCH.INFO[self.branch_keys.BRANCH_ID];
				$(`${self.id_list.COMPANY_DETAILS} [function=title]`).html(main.data_list.BRANCH.INFO[self.branch_keys.NAME]);
				$(`${self.id_list.COMPANY_DETAILS} [function=address]`).html(main.data_list.BRANCH.INFO[self.branch_keys.ADDRESS]);
				$(`${self.id_list.COMPANY_DETAILS} [function=image]`).attr('src',settings.paths.image.BRANCH_LOGO(branch_id));
				$(`${self.id_list.COMPANY_DETAILS} [function=min_price]`).html(main.data_list.BRANCH.INFO[self.branch_keys.TAKE_AWAY_AMOUNT]);
				$(`${self.id_list.COMPANY_DETAILS} [function=min_time]`).html(main.data_list.BRANCH.INFO[self.branch_keys.TAKE_AWAY_AMOUNT]);
				$(`${self.id_list.COMPANY_DETAILS} [function=categories]`).html(self.get_categories());

				let value = "";
				switch (main.data_list.BRANCH.INFO.table_type){

					case helper.db.branch_tables_static.TABLE:
					case helper.db.branch_tables_static.PERSON_SALE:
					case helper.db.branch_tables_static.OTHER_SALE:
						value = `${language.data.TABLE_ORDER} (${main.data_list.BRANCH.INFO.section_name}-${main.data_list.BRANCH.INFO.table_no})`;
						break;
					case helper.db.branch_tables_static.TAKE_AWAY: value = language.data.TAKEAWAY; break;
					case helper.db.branch_tables_static.COME_TAKE: value = language.data.COME_TAKE; break;
					case helper.db.branch_tables_static.DIGITAL_MENU: value = language.data.DIGITAL_MENU;
						$("#navbar_main").remove();
						$("#navbar_add_basket").remove();
						$(".e_top_nav_basket_btn").remove();

						if(data.error_code === settings.error_codes.IP_BLOCK){
							Swal.fire({
								title: language.data.QRCODE_SECURTY,
								html: language.data.IP_BLOCK_NO_ACCESS,
								showCancelButton: false,
								showConfirmButton: false
							})
						}
						break;
				}
				if (main.data_list.BRANCH.INFO.table_type !== helper.db.branch_tables_static.SAFE){
					$(self.class_list.COMPANY_MENU).addClass("disable-notification");
					$(self.class_list.NOTIFICATION_SHOW_BTN).remove();
				}

				switch (main.data_list.BRANCH.INFO.table_type){
					case helper.db.branch_tables_static.SAFE:
					case helper.db.branch_tables_static.DIGITAL_MENU:
					case helper.db.branch_tables_static.PERSON_SALE:
					case helper.db.branch_tables_static.OTHER_SALE:
						$(`${self.id_list.COMPANY_DETAILS} [function=min_price]`).closest(".item").remove();
						$(`${self.id_list.COMPANY_DETAILS} [function=min_time]`).closest(".item").remove();
						$(`.company.info .details`).css("grid-template-columns","1fr")
						break;
				}

				$(`${self.id_list.COMPANY_DETAILS} [function=table_type]`).html(value);
				$(".preloader").fadeOut();
				setTimeout(function (){$(".preloader").remove();},500)
			}
		},
		get_categories: function (id = 0){
			let self = this;
			let html = "";

			if (id === 0){
				$(`#page_company_details [function=bread_crumb]`).html(language.data.CATEGORIES);
			}
			main.data_list.CATEGORIES.forEach(function (data){
				if (data.active !== 0 && data.name !== null && data.name !== "") {
					if (data.main_id === id){
						html += self.create_elements(self.element_type.CATEGORY,data)
					}else if(data.id === id){
						$(`#page_company_details [function=bread_crumb]`).append("/"+data.name);
					}
				}
			});

			return html;
		},
		get_products: function (category_id= 0){
			let self = this;
			let html = "";
			if (category_id > 0) {
				main.data_list.PRODUCTS.forEach(function (data){
					if(data.active !== 0 && data.name !== null && data.name !== ""){
						if(parseInt(data.category_id) ===category_id){
							html += self.create_elements(self.element_type.PRODUCT,data);
						}
					}
				});
				html += '<div style="padding-bottom:100px"></div>';
				$(`#page_company_details [function=products]`).html(html);
			}else if(category_id === 0) {
				if (is_open) {
					is_open = false;

				}

				let last_category_id = 0;
				main.data_list.PRODUCTS.forEach(function (data){
					if(data.active !== 0 && data.name !== null && data.name !== ""){
						if (last_category_id === 0 || last_category_id !== data.category_id) {
							let category = array_list.find(main.data_list.CATEGORIES,data.category_id,"id");
							if (typeof category === "undefined"){
								category = [];
								category.name = language.data.UN_CATEGORY;
							}else if(category.active === 0) return;
							//html += create_title(category.name)
							//$(`#page_company_details [function=products]`).append(create_title(category.name));
							html += create_title(category.name);
							last_category_id = category.id;
						}
						if (last_category_id === data.category_id ){
							//html += self.create_elements(self.element_type.PRODUCT,data)
							//$(`#page_company_details [function=products]`).append(self.create_elements(self.element_type.PRODUCT,data))
							html += self.create_elements(self.element_type.PRODUCT,data)
						}
					}
				});
				html += '<div style="padding-bottom:100px"></div>';
				$(`#page_company_details [function=products]`).html(html);
			}

			return html;

			function create_title(name){
				return `<h6 class="category-title text-left text-secondary">${name}</h6>`;
			}
		},
		get_product_details: function (change= false,basket_change = false){
			//#page_product_details
			let self = this;
			let product = array_list.find(main.data_list.PRODUCTS,self.variable_list.SELECTED_PRODUCT_ID,"id");

			if (!change){ //click on product to get option items but update price data when option selection changes
				let image = server.is_valid_url(product.image) ? product.image : (settings.paths.image.PRODUCT(main.data_list.BRANCH.INFO.id)+"/"+product.image);
				self.get_product_options(product);
				self.variable_list.SELECTED_PRODUCT_ID = product.id;
				self.variable_list.SELECTED_PRODUCT_NAME = product.name;
				self.variable_list.SELECTED_PRODUCT_PRICE = product.price; // one product price no options
				self.variable_list.SELECTED_PRODUCT_IMAGE = product.image;
				$(`#page_product_details [area=image]`).attr("src",image);
				$(`#page_product_details [area=title]`).html(product.name)
				$(`#page_product_details [area=detail]`).html(product.comment);
			}

			if (!basket_change){
				self.variable_list.SELECTED_PRODUCT_OPTION_PRICE = (self.selected_option_values()["price"]);;
			}
			self.variable_list.SELECTED_PRODUCT_TOTAL_PRICE = (self.variable_list.SELECTED_PRODUCT_OPTION_PRICE + product.price) * (self.variable_list.SELECTED_PRODUCT_AMOUNT) // ((product + option) * qty) price
			$(`#product_detail_total_price`).html(self.variable_list.SELECTED_PRODUCT_TOTAL_PRICE.toFixed(2)+" ₺");
			pages.open("product_details");
		},
		get_product_options: function (product){
			let elements = "";
			let self = this;
			let linked_options = array_list.find_multi(main.data_list.PRODUCT_LINKED_OPTIONS, product.id, "product_id");
			linked_options.forEach(linked_option => {
				let options = array_list.find_multi(main.data_list.PRODUCT_OPTIONS, linked_option.option_id, "id");
				let items ="";
				options.forEach(option => {
					let option_items = array_list.find_multi(main.data_list.PRODUCT_OPTION_ITEMS, option.id, "option_id");
					option_items.forEach(option_item => {
						items += create_element_items(option.type,option_item,option_item.is_default);
					});
					elements += create_element(option.type,option.name,option.id,linked_option.max_count,items);
				});
			});

			function create_element(type,option_name,option_id,max_count,items){
				let html = "";
				switch (type){
					case self.option_types.MATERIALS:
						html = `
							<div option-id="${option_id}" type="${self.option_types.MATERIALS}" class="options materials">
								<div class="title">
									<h3 class="p-0">${option_name}</h3>
									<span>${language.data.WANT_TO_OUT_MATERIALS}</span>
								</div>
								<div class="items e_materials_btn">
									${items}
								</div>
							</div>`;
						break;

					case self.option_types.SINGLE_SELECT:
						html = `                        
								<div option-id="${option_id}" type="${self.option_types.SINGLE_SELECT}" class="options single-select">
									<div class="title"><h3 class="p-0">${option_name}</h3></div>
									<select class="form-input w-100" required>
										<option value="" selected>Lütfen Seçim Yapınız</option>
										${items}
									</select>
								</div>`;

						break;
					case self.option_types.MULTI_SELECT:
						html =`
							<div option-id="${option_id}" type="${self.option_types.MULTI_SELECT}" max_count="${max_count}" class="options multi-select">
								<div class="title"><h3 class="p-0">${option_name}</h3></div>
								<div class="list">
									${items}
								</div>
							</div>
							`;
						break;

				}
				return html;
			}

			function create_element_items(type,option_item,is_default = 0){
				let html = "";
				let selected;
				let show_price =  (option_item.price !== 0) ? `` + ((option_item.price > 0) ? `+` : "") + `${option_item.price.toFixed(2)}` : "";
				switch (type){
					case self.option_types.MATERIALS:
						html += `<span item-id="${option_item.id}" price="0">${option_item.name}</span>`
						break;
					case self.option_types.SINGLE_SELECT:
						selected = (is_default === 1) ?  "selected" : "";
						html = `<option ${selected} price="${option_item.price}" value="${option_item.id}">${option_item.name} ${show_price}</option>`;
						break;
					case self.option_types.MULTI_SELECT:
						selected = (is_default === 1) ? "checked" : "";
						html =`
								<div class="item" item-id="${option_item.id}" price="${option_item.price}">
									<div class="title"><label for="check_select-${option_item.id}">${option_item.name}</label></div>
									<div class="d-blok text-right pr-1 pt-1"><span>${show_price}</span></label></div>
									<div class="check"><input ${selected} id="check_select-${option_item.id}" type="checkbox"></div>
								</div>`;
						break;

				}
				return html;
			}


			$("#page_product_details [area=options]").html(elements);

		},
		get_basket: function (create = true){
			let self = this;
			let html = "";
			let total_price = 0;
			self.variable_list.BASKET.forEach(function (item,index){
				if (create){
					html += create_element(item,index)
				}
				total_price += item.product.total_price;
				index++;
			})
			if (main.data_list.USER.INFO.user_id > 0){
				if (total_price > 0){
					$("#page_basket [function=send]").show()
				}else {
					$("#page_basket [function=send]").hide();
				}
				$("#page_basket [function=register_btn]").hide();
			}else {
				$("#page_basket [function=register_btn]").show();
			}

			if (create) $("#page_basket [area=list]").html(html);

			$("#page_basket [area=basket_total_price]").html(total_price + " ₺")
			function create_element(data,index){
				let options= "";
				let materials = "";
				let opt,item;
				for (const i in data.options) {
					opt = data.options[i]
					if (opt !== undefined){
						for (const i2 in opt.items) {
							item = opt.items[i2]
							if (item !== undefined){
								if (opt.type === self.option_types.MATERIALS){
									materials += `${array_list.find(main.data_list.PRODUCT_OPTION_ITEMS,item.id,"id").name} `;
								}else {
									options += `<li>${array_list.find(main.data_list.PRODUCT_OPTION_ITEMS,item.id,"id").name}</li>`;
								}
							}
						}
					}
					if (opt.type === self.option_types.MATERIALS && materials !== ""){
						options += `<li>Olmasın: ${materials}</li>`;
						materials = "";
					}
				}

				let branch_id = main.data_list.BRANCH.INFO.id;
				let html = `
				<div class="order e_order animate__animated animate__fadeInUp animate__faster" index="${index}">
					<div class="product">
						<div class="image">
							<img src="${data.product.image}" alt="">
							<img src="${(server.is_valid_url(data.product.image)) ? data.product.image : `../images/branches/${branch_id}/product/${data.product.image}`}" alt="img">
						</div>
						<div class="content">
							<div class="title"><span>${data.product.name}</span></div>
							<div class="price e_price"><span class="price">${parseFloat(data.product.total_price).toFixed(2)}₺</span></div>
						</div>
						<div class="amount">
							<button class="bg-none e_product_operation" function="-"><i class="fa fa-minus"></i></button>
							<span class="e_product_count">${data.product.amount}</span>
							<button class="bg-none e_product_operation" function="+"><i class="fa fa-plus"></i></button>
						</div>
						<button class="e_del_order" type="bg-none"><i class="fa fa-trash"></i></button>
					</div>
					<div class="options">
                    	<ul>
							${options}
                    	</ul>
					</div>
				</div>`;

				return html;
			}
		},
		get_orders: function (send = true){
			let self = this;
			if (send) {
				self.set({set_type: self.set_type.GET_ORDERS});
			}else{
				console.log("hi ther bro look at")
				let html = "";
				let order_id = 0;
				let box = "";
				let products = "";
				let total = 0;
				main.data_list.GET_ORDERS.forEach(function (value){
					if (order_id === 0 || order_id !== value.id){
						create_box();
						order_id = value.id;
						box = `<div class="box">
									<h5>${value.branch_name}</h5>
									<p>${language.data.ORDER_DATE}: ${value.date_start}</p>
									<p>${language.data.ORDER_NUMBER}: ${value.id}</p>
									<div class="products">
										<div class="middle">
											[product]
										</div>
										<div class="bottom">
											<p>${language.data.TOTAL_AMOUNT}: <span>[total_price]₺</span></p>
										</div>
									</div>
								</div>`;
					}
					products  += `<p> ${value.name} <span>${(parseFloat(value.price).toFixed(2))} ₺</span> </p>`;
					total  += parseFloat(value.price);
				});
				create_box();

				function create_box(){
					if (box !== "" &&  order_id !== 0) {
						box = box.replaceAll("[product]",products);
						box = box.replaceAll("[total_price]",total);
						html += box;
						box = ""; products = ""; total = 0;
					}
				}

				$("#page_order_histroy [area=order_history]").html(html);
			}



		},
		set: function(form_data,success_function = null,async=true){
			let self = this;
			$.ajax({
				url: `${default_ajax_path}set.php`,
				type: "POST",
				data: form_data,
				async: async,
				success: function (data) {
					data = JSON.parse(data);
					console.log(data);
					if (success_function !== null){
						success_function(data);
					}

					if (form_data.set_type === self.set_type.GET_BRANCH){
						console.log("GET BRANCH")
						console.log(data)
						if (data.status){
							self.get(false, data);
						}else {
							$("page").html(`<div class="text-center" style="padding-top:calc(50vh - 40px)"><h3>${language.data.NOT_RECOGNIZED_QR}</h3><p>${language.data.INVALID_QR_CODE}</p></div>`)
						}
					}else if(form_data.set_type === self.set_type.GET_ORDERS){
						main.data_list.GET_ORDERS = data.rows
						self.get_orders(false);
					}

				}, timeout: settings.ajax_timeouts.NORMAL
			});
		},
		set_order: function (order,takeaway = null,come_take=null){
			let self = this;


			$.ajax({
				url: `${default_ajax_path}set.php`,
				type: "POST",
				data: {order: order, takeaway:takeaway,come_take:come_take, set_type:self.set_type.SET_ORDER},
				success: function (data) {
					console.log(data);
					data = JSON.parse(data);
					console.log(data);
					self.variable_list.BASKET = Array()
					pages.close("basket");
					helper_sweet_alert.success(`${language.data.ORDER_SENT}`);

				},error: helper_sweet_alert.close(), timeout: settings.ajax_timeouts.NORMAL
			});
		},
		selected_option_values: function (){
			let self = this;
			let data_list = Array();
			let data = Array();
			let item_id = 0;
			let item_price = 0;
			let index = 0;
			let sub_index = 0;
			let selected = false;
			let items = null;
			let total_price = 0;
			Array.from($("#page_product_details [area=options] [option-id],#page_product_details [area=options] [option-id]")).forEach(function(e){
				let element = $(e);
				let option_id  = element.attr("option-id");
				let option_type  = parseInt(element.attr("type"))
				if (option_type === 2){
					items = element.children("select");
				}else {
					items = element.children().children("[item-id]");
				}
				//console.log(items);
				if(!Array.isArray(data[index])) {
					data[index] = {}
					data[index]["id"] = option_id;
					data[index]["type"] = option_type;
					//data[index]["name"] = array_list.find(main.data_list.PRODUCT_OPTIONS,parseInt(option_id),"id").name;
					if (data[index]["items"] === undefined){
						data[index]["items"] = {}
					}
				}
				Array.from(items).forEach(function(item){
					switch (option_type){
						//materials
						case self.option_types.MATERIALS:
							selected = $(item).hasClass("selected")
							if(selected){
								item_id = parseInt($(item).attr("item-id"))
								item_price = parseFloat($(item).attr("price"));
							}
							break;
						//single select
						case self.option_types.SINGLE_SELECT:
							let select_element = $(item).children("select option").filter(':selected');
							if (select_element.val() !== ""){
								selected = true;
								item_id = parseInt(select_element.attr("value"))
								item_price = parseInt(select_element.attr("price"))
							}
							break;
						//multi select
						case self.option_types.MULTI_SELECT:
							selected = $(item).children().children("input").is(':checked');
							if(selected){
								item_id = parseInt($(item).attr("item-id"))
								item_price = parseFloat($(item).attr("price"));
							}
							break;
					}
					if (selected) {
						total_price += item_price;
						data[index]["items"][sub_index] = {id:item_id,price:item_price}
						sub_index++;
						item_id = null;
						item_price = null;
					}
				})
				if(sub_index > 0) index++;
				sub_index = 0;
			})
			data_list["options"] = data;
			data_list["price"] = total_price;
			return data_list;
		},
		select_product_values: function (){
			let self = this;
			return {
				id: self.variable_list.SELECTED_PRODUCT_ID,
				name: self.variable_list.SELECTED_PRODUCT_NAME,
				amount: self.variable_list.SELECTED_PRODUCT_AMOUNT,
				total_price: self.variable_list.SELECTED_PRODUCT_TOTAL_PRICE,
				image: self.variable_list.SELECTED_PRODUCT_IMAGE,
			}

		},
		create_elements(element_type,data){
			let self = this;
			let value = "";
			switch (element_type){
				case self.element_type.CATEGORY:
					value = create_category_elements(data);
					break;
				case self.element_type.PRODUCT:
					value = create_product_elements(data);
					break;
			}
			return value;

			//
			function create_category_elements(data){
				let image = (data.product_id > 0) ?  array_list.find(main.data_list.PRODUCTS,data.product_id,"id") : "";
				image = (typeof image === "undefined") ? "" : image.image;

				return`<div class="box" category-id="${data.id}">
					<div class="image"><img src="${(server.is_valid_url(image)) ? image: settings.paths.image.BRANCH_LOGO(main.data_list.BRANCH.INFO.id)}"  alt="${data.name}">
					</div>
					<div class="title"><span>${data.name}</span></div>
				</div>`;
			}

			function create_product_elements(data){
				let image = server.is_valid_url(data.image) ? data.image : (settings.paths.image.PRODUCT(main.data_list.BRANCH.INFO.id)+"/"+data.image);
				return `
					<div class="product" product-id="${data.id}">
						<div class="image">
							<img src="${image}" alt="">
						</div>
						<div class="details">
							<div class="title"><span>${data.name}</span></div>
							<div class="content"><span>${data.comment}</span></div>
							<div class="point"></div>
						</div>
						<div class="price">
							<span class="price-menu">${data.price}₺</span>
							<button type="button" class="btn btn-light btn-product" style=""><i class="fa fa-plus fa-color-5"></i></button>
						</div>
					</div>
				`;
			}
		},
		delete_basket_items: function (del_index){
			let self = this;
			self.variable_list.BASKET.splice(del_index,1);
			self.get_basket();
		},
		send_toast: function (title = language.data.PRODUCT_ADD_TO_CART){
			$.Toast(`<i class='fa fa-check'></i> ${title}`, "", "success", {
				position_class:"toast-top-right",
				has_icon:false,
				has_close_btn:true,
				stack: false,
				fullscreen:true,
				timeout:1000,
				sticky:false,
				has_progress:false,
				rtl:false,
			});
		},
		initialize: function (){
			let self = this;
			function set_events(){
				$(document).on('_pop_pages', function (event) {
					switch (event.detail.type){
						case pages.event_types.CLOSE:
							if (event.detail.name === "product_details" || event.detail.name === "login_and_register"){
								$("#navbar_main").show();
								$("#navbar_add_basket").hide();
								bottom_navbar = true;
							}else if(event.detail.name === "order_confirm"){
								$("#navbar_main").hide();
								$("#navbar_add_basket").hide();
							}
							break;
						case pages.event_types.OPEN: case pages.event_types.CLOSE_ALL:
							set_uri_patch(event.detail.name)
							if (event.detail.name === "product_details"){
								$("#navbar_main").hide();
								$("#navbar_add_basket").show();
							}else if(event.detail.name === "basket"){
								self.get_basket();
							}else if(event.detail.name === "order_history"){
								self.get_orders();
							}
							break;
					}
				})

				$(self.id_list.NAVBAR_ADD_BASKET).click(function (){
					$(self.id_list.PRODUCT_ADD_TO_BASKET).trigger("click");
				})
				$(self.class_list.GET_CATEGORIES).click(function (){
					//$(`#page_company_details [area=products]`).html("");
					$(`#page_company_details [function=products]`).children().hide()
					$(`#page_company_details [function=categories]`).html(self.get_categories());
					$("#page_company_details .pop-page-in").animate({ scrollTop: 160},"slow");
					$(`#page_company_details [area=categories]`).show();
					$(`#page_company_details [area=products]`).hide();
				});
				$(self.class_list.GET_PRODUCTS).click(function (){
					self.get_products();
					//$(`#page_company_details [function=products]`).html(self.get_products());
					$("#page_company_details .pop-page-in").animate({ scrollTop: 160},"slow");
					$(`#page_company_details [area=categories]`).hide();
					$(`#page_company_details [area=products]`).show();
				});
				$(self.class_list.SELECT_LANGUAGE).click(function (){
					let element = "";
					let languages = [
						{name:"Türkçe",		 language:helper.db.language_columns.TURKISH},
						{name:"English",	 language:helper.db.language_columns.ENGLISH},
						{name:"Deutsch",	 language:helper.db.language_columns.GERMAN},
						{name:"Русский",	 language:helper.db.language_columns.RUSSIAN},
						{name:"Français",	 language:helper.db.language_columns.FRENCH},
						{name:"Nederlands",	 language:helper.db.language_columns.DUTCH},
						{name:"عربى",		 language:helper.db.language_columns.ARABIC},
						{name:"Español",	 language:helper.db.language_columns.SPANISH},
						{name:"Italiano",	 language:helper.db.language_columns.ITALIAN},
						{name:"Português",	 language:helper.db.language_columns.PORTUGUESE},
						{name:"Română",		 language:helper.db.language_columns.ROMANIAN},
						{name:"中国人",		 language:helper.db.language_columns.CHINESE},
					]
					languages.forEach((item) => element+= `<button language="${item.language}" class="btn btn-primary w-100 mb-2">${item.name}</button>`)
					Swal.fire({
						showCancelButton: false, showConfirmButton: false,
						html: element
					})
				});

				$(document).on("click",`button[language] `,function (){
					let attr = $(this).attr("language");
					self.set({set_type:self.set_type.LANGUAGE,language:attr},function (data) {
						location.reload();
					})
				});
				$(document).on("click", `${self.class_list.MATERIALS_BTN} span`, function () {
					let element = $(this);
					console.log(element)
					if(element.hasClass("selected")){
						element.removeClass("selected");
					}else{
						element.addClass("selected");
					}
				});
				$(document).on("click", `${self.id_list.COMPANY_DETAILS} .categories .list .box`,function (){
					let element = $(this);
					let category_id = parseInt(element.attr("category-id"));
					$(`#page_company_details [function=categories]`).html(self.get_categories(category_id));
					$(`#page_company_details [function=products]`).html(self.get_products(category_id));
					$(`#page_company_details [area=products]`).show();
					$("#page_company_details .pop-page-in").animate({ scrollTop: 160},"slow");

				})
				$(document).on("click",	`${self.id_list.COMPANY_DETAILS}  [product-id]`,function (){
					let element = $(this);
					self.variable_list.SELECTED_PRODUCT_ID = parseInt(element.attr("product-id"));
					$("#page_product_details .e_product_count").html("1")
					self.get_product_details();
				})
				$(document).on("click",	`${self.id_list.PRODUCT_DETAILS} .e_product_operation`,function (){
					let element = $(this);
					let count_element = $("#page_product_details .e_product_count");
					let type = element.attr("function");
					let count = parseInt(count_element.html());

					switch (type){
						case "+":
							if (count < 30) {
								count++;
								count_element.html(count);
							}
							break;
						case "-":
							if (count > 1) {
								count--;
								count_element.html(count);
							}
							break;
					}
					self.variable_list.SELECTED_PRODUCT_AMOUNT = count;
					self.get_product_details(true);
				});
				$(document).on("submit",`${self.id_list.PRODUCT_DETAILS} form`,function (e){
					e.preventDefault();
					let element = $(this);

					self.variable_list.BASKET.push({
						product: self.select_product_values(),
						options: self.selected_option_values()["options"],
					});
					basket = self.variable_list.BASKET;
					self.send_toast();
					pages.close("product_details");
					return false;
				})
				$(document).on("change",`${self.id_list.PRODUCT_DETAILS} [option-id]`, function () {
					self.get_product_details(true);
				});
				$(document).on("click",	`${self.id_list.PRODUCT_DETAILS} span[item-id]`,function(){
					self.get_product_details(true);
				});

				$(document).on("click",	`${self.id_list.BASKET} .e_product_operation`,function (){
					let element = $(this);
					let closest = element.closest(".e_order");
					let index = parseInt(closest.attr("index"));
					let count_element = $(closest).children(".product").children(".amount").children(".e_product_count");
					let type = element.attr("function");
					let count = parseInt(count_element.html());
					let one_price = self.variable_list.BASKET[index].product.total_price / self.variable_list.BASKET[index].product.amount;
					switch (type){
						case "+":
							if (count < 30) {
								count++;
								self.variable_list.BASKET[index].product.total_price += one_price;
								self.variable_list.BASKET[index].product.amount += 1;
							}
							break;
						case "-":
							if (count > 1) {
								count--;
								self.variable_list.BASKET[index].product.total_price -= one_price;
								self.variable_list.BASKET[index].product.amount -= 1;
							}
							break;
					}
					if (type === "-" || type === "+"){
						count_element.html(count);
						self.get_basket(false);
						$(`#page_basket .e_order[index="${index}"] .e_price span`).html(self.variable_list.BASKET[index].product.total_price+"₺")
					}
					self.variable_list.SELECTED_PRODUCT_AMOUNT = count;
				});
				$(document).on("click",	`${self.id_list.BASKET} .e_del_order`,function (){
					self.delete_basket_items(parseInt($(this).closest(".e_order").attr("index")));
				});
				$(document).on("click",	`${self.id_list.BASKET} [function=send]`,function (){
					switch (main.data_list.BRANCH.INFO.table_type){
						case helper.db.branch_tables_static.SAFE:
							Swal.fire({
								title: language.data.ORDER_CONFRIM,
								icon: 'warning',
								showCancelButton: true,
								confirmButtonText: 'Evet',
								cancelButtonText: "Hayır",
							}).then((result) => {
								if (result.value) {
									self.set_order(self.variable_list.BASKET);
								}
							});
							break;
						case helper.db.branch_tables_static.TAKE_AWAY:
							let total_price = 0;
							self.variable_list.BASKET.forEach(function (item){total_price += item.product.total_price})

							if (total_price >= main.data_list.BRANCH.INFO.take_away_amount){
								self.variable_list.SEND_TAKEAWAY.TOTAL_PRICE = total_price;
								reset();
								let address_select = ``;
								address_select += create_select_option("", language.data.SELECT_VALID_ADDRESS);
								main.data_list.USER.ADDRESSES.forEach(function (item){
									address_select += create_select_option(item.neighborhood,item.title,item.id);
								})

								let payment_select = ``;
								payment_select += create_select_option("", language.data.SELECT_PAYMENT_METHOD);
								payment_select += create_select_option(1, language.data.CASH);
								payment_select += create_select_option(2,language.data.CREDIT_CARD);
								main.data_list.BRANCH.PAYMENT_TYPES.forEach(function (item){
									if (item.active_take_away === 1 && item.type_id !== 6 ){
										let payment = array_list.find(main.data_list.PAYMENT_TYPES,item.type_id,"id")
										payment_select += create_select_option(payment.id,payment.name);
									}
								})

								$(`${self.id_list.ORDER_CONFIRM} [area=address-selection]`).html(address_select)
								$(`${self.id_list.ORDER_CONFIRM} [area=payment-selection]`).html(payment_select)

								pages.open("order_confirm");

								function create_select_option(value,display_value,user_select_id=""){
									let atr = (user_select_id !== "") ? `user-select-id=${user_select_id}` : "";
									return `<option ${atr} value="${value}">${display_value}</option>`;
								}
								function reset(){
									$(`${self.id_list.ORDER_CONFIRM} [area=payment-alert]`).hide()
									$(`${self.id_list.ORDER_CONFIRM} [area=address-alert]`).hide()
									self.variable_list.SEND_TAKEAWAY.PAYMENT_ID = 0;
									self.variable_list.SEND_TAKEAWAY.PAYMENT_SELECT = false;
									self.variable_list.SEND_TAKEAWAY.USER_ADDRESS_ID = 0;
									self.variable_list.SEND_TAKEAWAY.ADDRESS_SELECT = false;
								}
							}else helper_sweet_alert.error(`Minumum Tutar Geçerli Değil`, `Minimum Tutar ${main.data_list.BRANCH.INFO.take_away_amount}₺ dir!<br>Sepete ${main.data_list.BRANCH.INFO.take_away_amount - total_price}₺ daha ürün eklemelisiniz. `)
							break;
						case helper.db.branch_tables_static.COME_TAKE:break;
						case helper.db.branch_tables_static.DIGITAL_MENU :break;
					}
				})

				$(document).on("change",`${self.id_list.ORDER_CONFIRM} [area]`,function (){
					let status = false;
					let element = $(this);
					let area = element.attr("area");
					switch (area){
						case "payment-selection":
							if (element.val() > 0){
								let payment =   $(`${self.id_list.ORDER_CONFIRM} [area=payment-selection] option:selected`).text();

								self.variable_list.SEND_TAKEAWAY.PAYMENT_SELECT = true;
								self.variable_list.SEND_TAKEAWAY.PAYMENT_ID = parseInt(element.val());
								self.variable_list.SEND_TAKEAWAY.PAYMENT_NAME = payment;

								$(`${self.id_list.ORDER_CONFIRM} [area=payment-alert]`)
									.html(`${language.data.PAYING_AT_DOOR}: ${payment} ${language.data.SELECTED}`)
									.removeClass("alert-info alert-danger")
									.addClass("alert-success")
									.fadeIn()
							}else {
								self.variable_list.SEND_TAKEAWAY.PAYMENT_SELECT = false;
								self.variable_list.SEND_TAKEAWAY.PAYMENT_ID = 0;
								$(`${self.id_list.ORDER_CONFIRM} [area=payment-alert]`)
									.html(`${language.data.SELECT_VALID_PAYMENT_METHOD}`)
									.removeClass("alert-info alert-success")
									.addClass("alert-danger")
									.fadeIn()
							}
							break;
						case "address-selection":
							main.data_list.ACCEPTED_ADDRESS.forEach(function (item){
								if (parseInt(element.val()) === item.neighborhood_id) {
									status = true; return null;
								}
							});
							if (status) {
								$(`${self.id_list.ORDER_CONFIRM} [area=address-alert]`).html(`${language.data.VALID_ADDRESS}`).removeClass("alert-info alert-danger").addClass("alert-success").show()
								self.variable_list.SEND_TAKEAWAY.ADDRESS_SELECT = true
								self.variable_list.SEND_TAKEAWAY.USER_ADDRESS_ID = $(`${self.id_list.ORDER_CONFIRM} [area=address-selection] option:selected`).attr("user-select-id")
							}else {
								self.variable_list.SEND_TAKEAWAY.ADDRESS_SELECT = false;
								self.variable_list.SEND_TAKEAWAY.USER_ADDRESS_ID = 0;

								$(`${self.id_list.ORDER_CONFIRM} [area=address-alert]`)
									.html(`${language.data.NOT_DELIVER_TO_ADDRESS}`)
									.removeClass("alert-info alert-success")
									.addClass("alert-danger")
									.fadeIn()
							}
							break;
					}
					//console.log(self.variable_list.SEND_TAKEAWAY)
					if (self.variable_list.SEND_TAKEAWAY.PAYMENT_SELECT && self.variable_list.SEND_TAKEAWAY.ADDRESS_SELECT){
						$(`${self.id_list.ORDER_CONFIRM} button[function=send-takeaway]`).prop("disabled","");
					}else {
						$(`${self.id_list.ORDER_CONFIRM} button[function=send-takeaway]`).prop("disabled","disabled");
					}
				})
				$(document).on("click", `${self.id_list.ORDER_CONFIRM} [function=send-takeaway]`,function (){
					Swal.fire({
						html: `<h4>${language.data.ORDER_CONFRIM}</h4>
								<b>${language.data.ORDER_TYPE}:</b>${language.data.TAKEAWAY}<br><b>${language.data.TOTAL_AMOUNT}:</b> ${self.variable_list.SEND_TAKEAWAY.TOTAL_PRICE}₺<br><b>${language.data.PAYMENT_METHOD}: </b> ${self.variable_list.SEND_TAKEAWAY.PAYMENT_NAME}`,
						icon: 'warning',
						showCancelButton: true,
						confirmButtonText: `${language.data.APPROVE}`,
						cancelButtonText: `${language.data.CANCEL}`,
					}).then((result) => {
						if (result.value) {
							let takeaway = {
								"payment_id": self.variable_list.SEND_TAKEAWAY.PAYMENT_ID,
								"user_address_id": self.variable_list.SEND_TAKEAWAY.USER_ADDRESS_ID,
								"total_price": self.variable_list.SEND_TAKEAWAY.TOTAL_PRICE,
								"note": $(`${self.id_list.ORDER_CONFIRM} [area=order-note]`).html()
							}
							self.set_order(self.variable_list.BASKET, takeaway);
							pages.close_all("company_details");
						}
					});
				});

				let scroll_show = true;
				$(".pop-page-in").on( 'scroll', function(){
					if (scroll_show){
						scroll_show = false;
						let p = $( ".products .list .product" );
						Array.from(p).forEach(function(e){
							let top = $(e).offset().top;
							if(top < innerHeight +1 && top > 0){
								$(e).css("opacity",1);
							}else {
								$(e).css("opacity",0);
							}
						})
						scroll_show = true;
					}
				})
			}
			set_events();
			self.get();

			$("#navbar_main").show();
			$("#navbar_add_basket").hide();
			bottom_navbar = true;

		}
	};

	page_panel.notification  = {
		class_list: {
			OPEN_BUTTON: ".e_notification_show",
			SEND_BUTTON: ".e_send_notification",
		},
		variable_list: {
			elements : ""
		},
		crete_elements: function(){
			let elements = "";
			main.data_list.NOTIFICATIONS.forEach(function (item){
				if (item.active === 1){
					elements += `<a href="#" notification-id="${item.id}" class="e_send_notification w-100 btn btn-s1 d-block mb-2">${item.name}</a>`;
				}
			})
			return elements;
		},
		set: function(form_data, success_function = function (){}){
			let self = this;
			$.ajax({
				url: `${default_ajax_path}set.php`,
				type: "POST",
				data: form_data,
				success: function (data) {
					data = JSON.parse(data);
					console.log(data);
					success_function(data);
				}, timeout: settings.ajax_timeouts.NORMAL
			});
		},
		initialize: function (){
			let self = this;
			function set_events(){
				$(document).on("click",self.class_list.OPEN_BUTTON,function (){
					//if (main.data_list.USER.INFO.user_id > 0){
						Swal.fire({
							title: `<strong>${language.data.SERVICES}</strong>`,
							html: self.crete_elements(),
							showConfirmButton: false
						})
					/*}else {
						pages.open("login_and_register");
					}*/
				});
				$(document).on("click",self.class_list.SEND_BUTTON,function (){
					let element = $(this);
					let id = parseInt(element.attr("notification-id"));
					let name = element.text();
					Swal.fire({
						title: `"${name}"`,
						showDenyButton: true,
						showCancelButton: true,
						confirmButtonText: `${language.data.ACCEPT},${language.data.SUBMIT}`,
						denyButtonText: `${language.data.DECLINE},${language.data.CANCEL}`,
					}).then((result) => {
						if (result.value) {
							Swal.fire(language.data.REQUEST_SENT, '', 'success')
							self.set({
								set_type: set_type.NOTIFICATION,
								id: id
							})
						}
					})
				});

			}
			//self.crete_elements();
			set_events();
		}
	}

	return page_panel;
})();

let page_address = (function() {
	let default_ajax_path = `${settings.paths.primary.PHP}panel/`;
	function page_address(){
		address.initialize();
	}

	let address = {
		id_list: {
			REGISTER_FORM: "#register_form",
			VERIFY_FORM: "#verify_code",
		},
		set_type: {
			GET_ADDRESS: 3,
			SET_ADDRESS: 4,
			DEL_ADDRESS: 5,
		},
		type: {
			CITY : 1,
			TOWN : 2,
			DISTRICT : 3,
			NEIGHBORHOOD : 4,
			USER : 5,
			DEL_ADDRESS : 6,
			UPDATE_ADDRESS : 7,
			GET_ADDRESS_ELEMENTS : 8,
			ALL_TYPE: 9
		},variable_list: {
			SELECT_TYPE: null
		},
		set: function(form_data, success_function = function (){},sync = false){
			//let self = this;
			$.ajax({
				url: `${default_ajax_path}set.php`,
				type: "POST",
				data: form_data,
				sync: sync,
				success: function (data) {
					data = JSON.parse(data);
					//console.log(data);
					success_function(data);
				},error: helper_sweet_alert.close(), timeout: settings.ajax_timeouts.NORMAL
			});
		},
		create_options: function create_options(array,value_key,display_key){
			let html = "";
			html += `<option value="">Seçim Yapınız</option>`;
			if (array === undefined || array === null || array.length === 0) return html;

			array.forEach(function (e){
				html += `<option value="${e[value_key]}">${e[display_key]}</option>`;
			});
			return html;
		},
		add_address: function (){
			let self = this;
			$("#page_edit_address form input,#page_edit_address form select,#page_edit_address form textarea").val("").html("")
			$("#page_edit_address form [name=id]").val("0");
			self.get_address_select(Array(),self.type.ALL_TYPE);
		},
		get_address_select: function (data,type){
			let self = this;
			data = (data.rows !== undefined ) ? data.rows : data;
			if (type === undefined) { type = self.variable_list.SELECT_TYPE}
			switch (type){
				case self.type.CITY:
					$("#page_edit_address [function=1]").html(self.create_options(main.data_list.ADDRESS.CITY,"id","city"))
					break;
				case  self.type.TOWN:
					$("#page_edit_address [function=2]").html(self.create_options(data,"id","town"))
					break;
				case  self.type.DISTRICT:
					$("#page_edit_address [function=3]").html(self.create_options(data,"id","district"))
					break;
				case  self.type.NEIGHBORHOOD:
					$("#page_edit_address [function=4]").html(self.create_options(data,"id","neighborhood"))
					break;
				case  self.type.ALL_TYPE:
					$("#page_edit_address [function=1]").html(self.create_options(main.data_list.ADDRESS.CITY,"id","city"))
					$("#page_edit_address [function=2]").html(self.create_options(data,"id","town"))
					$("#page_edit_address [function=3]").html(self.create_options(data,"id","district"))
					$("#page_edit_address [function=4]").html(self.create_options(data,"id","neighborhood"))
					break;
			}
		},
		get_address: function (sync=false){
			let self = this;
			let data = {set_type: self.set_type.GET_ADDRESS, next_type: self.type.USER}
			self.set(data, function (value){
				set_elements(value)
				console.log("selam");
				console.log(value);
				main.data_list.USER.ADDRESSES = value.rows;
				console.log(value);
			},sync);
			function set_elements (data){
				let self = this;
				let html = "";
				if (data.rows.length > 0) {
					data.rows.forEach(function (v){
						html += create_element(v);
					});
				}else {
					html = "Kayıtlı Adres Yok!";
				}

				$("#page_address_list .e_address_list").html(html)

				function create_element(data){
					return `
                <div class="address-item shadow-sm" function="address_box" address-id="${data.id}">
					<div class="title" style="height: 30px;position: absolute;right: 20px;margin-top: -6px;">
						<span style="font-size: 25px;"><i operation="del" class="fa fa-trash float-right fa-color-12"></i></span>
						<span style="font-size: 25px;"><i operation="edit" class="fa fa-edit float-right pr-3 fa-color-11"></i></span>
					</div>
					
					<div class="address_detail">
						<table>
							<tr> <td class="title" colspan="2"><b>${data.title}</b></td> </tr>
							<tr> <td class="title">İl &amp; İlçe</td><td>${data.city_name} &amp; ${data.town_name}</td> </tr>
							<tr><td class="title">Semt</td><td>${data.district_name}</td> </tr>
							<tr><td class="title">Mahalle</td><td>${data.neighborhood_name}</td> </tr>
							<tr><td class="title">Sokak &amp; Cadde</td><td>${data.street}</td> </tr>
							<tr> <td class="title">No &amp; Kat &amp; Daire</td><td> ${data.apartment_number} /${data.floor}/ ${data.home_number} </td> </tr>
							<tr> <td class="title">Telefon</td><td>${data.phone}</td> </tr>
						</table>
					</div>
                </div>`;
				}
			}
		},
		get_city: function (){
			let self = this;
			let data = {set_type: self.set_type.GET_ADDRESS, next_type: self.type.CITY}
			self.set(data, function (value){
				console.log(value);
				if(variable.isset(()=> value.rows)) main.data_list.ADDRESS.CITY = value.rows
			},true);
			$("#page_edit_address [function=1]").html(self.create_options(main.data_list.ADDRESS.CITY,"id","city"))
		},
		operation: function(type,address_id){
			let self = this;
			let data = {}
			switch (type){
				case "edit":
					data = {
						set_type: self.set_type.GET_ADDRESS,
						next_type: self.type.USER,
						address_id: address_id
					}
					self.set(data,function(value){auto_fill(value)});
					pages.open("address_edit");
					break;
				case "del":
					data = {
						set_type: self.set_type.SET_ADDRESS,
						type: self.type.DEL_ADDRESS,
						address_id: address_id,
					}
					self.set(data,function (){del()});
					break;
			}
			function del(){
				helper_sweet_alert.success("Adres Silindi");
				self.get_address();
			}
			function auto_fill(data){
				self.get_address_select(data.custom_data.select.town,self.type.TOWN);
				self.get_address_select(data.custom_data.select.district,self.type.DISTRICT);
				self.get_address_select(data.custom_data.select.neighborhood,self.type.NEIGHBORHOOD);
				$("#address_form").autofill(data.rows[0]);
			}
		},
		save_data: function (data){
			let self = this;
			data["address_id"] = data.id;
			data["set_type"] = self.set_type.SET_ADDRESS;
			self.set(data,function (){success()});

			function success(){
				helper_sweet_alert.success("Adres Başarılı ile Kaydeydedildi");
				self.get_address()
				pages.close("address_edit");
			}
		},
		initialize: function (){
			let self = this;
			function set_events(){
				$(document).on("change","#page_edit_address .address_list select[function]",function (){
					let element = $(this);
					let type = parseInt(element.attr("function"));
					let data = {};
					data["id"] = element.val();
					data["set_type"] = self.set_type.GET_ADDRESS;
					data["next_type"] = 0;
					switch (type){
						case self.type.CITY:
							data["type"] = self.type.CITY;
							data["next_type"] = self.type.TOWN;
							$("#page_edit_address [function=3],#page_edit_address [function=4]").html(self.create_options())
							break;
						case  self.type.TOWN:
							data["type"] = self.type.TOWN;
							data["next_type"] = self.type.DISTRICT;
							$("#page_edit_address [function=4]").html(self.create_options())
							break;
						case  self.type.DISTRICT:
							data["type"] = self.type.DISTRICT;
							data["next_type"] = self.type.NEIGHBORHOOD;
							break;
					}
					if (data["next_type"] > 0){
						self.variable_list.SELECT_TYPE = data.next_type;
						self.set(data,function (data){self.get_address_select(data)});
					}
				})
				// send form data //
				$(document).on("submit","#page_edit_address form",function (){
					let data = $(this).serializeObject();
					self.save_data(data);
					return false;
				})
				$(document).on("click","[open_page=address_list]",function (){
					self.get_address();
				});
				$(document).on("click","[open_page=address_edit]",function (){
					self.add_address();
				});
				$(document).on("click","#page_address_list [function=address_box] [operation]",function (){
					let element = $(this);
					let operation = element.attr("operation");
					let id = $(this).closest("[function=address_box]").attr("address-id");
					self.operation(operation,id)
				});
			}
			set_events();
			self.get_city();
			self.get_address();
		}
	};

	return page_address;
})();

