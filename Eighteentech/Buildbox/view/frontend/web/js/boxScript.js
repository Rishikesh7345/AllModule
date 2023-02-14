require(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/url',
        'Magento_Catalog/js/price-utils'
    ],
    function ($, modal, url, priceUtils) {
        var options = {
            type: 'popup',
            responsive: true,
            modalClass: 'build-box',
            innerScroll: true,
            buttons: [{
                text: $.mage.__('Close'),
                class: 'modal-close',
                click: function () {
                    this.closeModal();
                }
            }]
        };

        var minProQty = '';
        var productLogoQty = 250;
        modal(options, $('#modal-content'));

        $("#modal-btn").on('click', function () { 
            $(".build-box").show();
             $.ajax({
                type: "post",
                url: url.build('buildbox/submit/Checkcartconfigproduct'),
                cache: false,
                showLoader: true,
                success: function (data) {
                           
                    $(".del_popup").find("#continue-button").removeClass("editContinueBtn");
                    $(".del_popup").find("#continue-button").removeClass("editContName");
                    $(".del_popup").find("#continue-button").addClass("continue-button");
                
                    $(".active_buildbox").show();
                    $(".modal-inner-wrap").show();
                    $('.build_box_step_1').hide();
                    $("#modal-content").modal("openModal");
                    $('#buildbox_note_popup').show();
                },
                error: function (xhr, status, error) {
                    console.error(xhr);
                }
            });
            
        });

        // Notice popup in build box 
        $('#buildbox_note_popup').find(".note-cancel-btn").click(function(){
            $('#buildbox_note_popup').hide();
            $('.build_box_step_1').hide();
            $(".build-box").hide();
            // $("#modal-overlay").removeClass("buildbox_modal_overlay");
        });
        $('#buildbox_note_popup').find(".note-ok-btn").click(function(){
            $('#buildbox_note_popup').hide();
            $(".build_box_step_1").addClass("active");
            $('.build_box_step_1').show();           
            // $("#modal-overlay").removeClass("buildbox_modal_overlay");
        });
        $("#product_check_resp").find(".note-cancel-btn").click(function(){
            $("#product_check_resp").hide();
        });
        $("#product_check_resp").find(".note-ok-btn").click(function(){
            $("#product_check_resp").hide();
        });

        $(".build_box_step_2").find("#prodQtyForBox").keyup(function(){
            var regex = /^\d*[.]?\d*$/;
            var qtyforbox = $(this).val();
            if(!regex.test(qtyforbox)){
                qtyforbox=''
                return false;
            }
        })

        // .............................. //
        $(".skip-btn2").click(function () {
            $(".modal-popup").hide();
            location.reload();
        });

        // Kit name validation
		$('#input-box-name').keypress(function (e) {
			var regex = new RegExp("^[a-zA-Z ]*$");
			var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
			if (regex.test(str)) {
				$(".next-btn").on('click', function () {
					var boxName = $(".input-box-name").val();
					if (boxName == "") {
						$(".input-box-name").css("border", "2px solid red");
						$(".next-btn").attr('disabled', false);
					} else {
						$(".box-title-name").text(boxName);
						$(".build_box_step_1").hide();
						$(".build_box_step_2").show();
					}
				});
				$("#invaildName").hide();
				return true;
			}
			else
			{
				e.preventDefault();
				$(".next-btn").off('click');
				$("#invaildName").show();
				return false;
			}
		});

        // show kit product list(build_box_step_2) after click next button 
        var qtyMsg ='yes';
        $(".next-btn2").on('click', function (e) {
            
            var proDim = [];
            var cartProduId = '';
            var prodQty = '';
            var checkNo = '';
            var checkVal = 0;
            var proQty = [];
            var proIdKit = {};
            var selectedProId ='';
            
            var storeProId=[];
            $(".build_box_step_2").find('.product-list').each(function () {
                //get values
          
                if ($(this).find('.proDimVal').prop('checked') == true) {   
                    selectedProId = $(this).find('input[name="getItem[]"]').val();
                    storeProId.push(selectedProId);
                    cartProduId = $(this).find('.proDimVal').attr('data-pro-id');
                    var proDimVal = $(this).find('.proDimVal').attr('data-dim');

                    proQty.push($(this).find(".product-qty").val());
                   
                    prodQty = $(this).find('.proDimVal').attr('prod-qty');
                    $("#errMsg").hide();
                    checkNo = 1;
                    proDim.push(proDimVal);
                  
                    var qtyInput = $(this).find('.qty-input').val();
                    var prodidforbox = $(this).find('.qty-input').attr("prodidforbox");
                    
                    if(qtyInput == 0){
                        checkNo=0;
                        if(qtyMsg =='yes' || checkNo==0 ){
                            qtyMsg='no';
                            checkVal=1;
                            
                            $(this).find('.eachQty').show();                                             
                        }
                    }else {
                        
                        proIdKit[prodidforbox] = qtyInput;                                           
                        $(this).find('.eachQty').hide();                        
                    }

                }
                else {                    
                    checkNo = $(".build_box_step_2").find('input[type="checkbox"]:checked').length;
                    if (checkNo == 0) {
                        $("#errMsg").show();
                    }
                }
            });
            minProQty = Math.min.apply(Math,proQty);
            
            $("#boxQty").val(minProQty);
            if (checkNo != 0 && checkVal != 1) {
                console.log(proIdKit,' proIdKit');
                $('input[name="cartSelectedPro"]').val(storeProId);
                $.ajax({
                    type: "post",
                    url: url.build('buildbox/submit/dimensionvalue'),
                    data: ({
                        proDim: proDim,
                        cartProduId: cartProduId,
                        prodQty: prodQty,
                        selectedProId: storeProId,
                        proIdKit: proIdKit
                    }),
                    cache: false,
                    showLoader: true,
                    success: function (data) {
                        $("#productBox").html(data);
                        $(".build_box_step_2").hide();
                        $(".build_box_step_3").show();
                        $(this).find('.eachQty').hide();
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr);
                    }
                });
                
            }else{
                $(this).find('.eachQty').show();
            }
            e.preventDefault();
        });

        $("#editBoxQty").keyup(function(){ 
			 
            var boxQty = $(this).val();
            if(parseInt(boxQty) >= productLogoQty){
               $("#respProDetails").find(".additional:nth-child(4)").show();
               $("#editRespOption").find(".additional:nth-child(4)").show();
            }else{
                $("#respProDetails").find(".additional:nth-child(4)").hide();
                $("#editRespOption").find(".additional:nth-child(4)").hide();
            }
            if($(this).val() <= editMinQty){
                $("#editQtyErr").css("color","red");
                $("#editMinQty").html(editMinQty);
                $("#editQtyErr").show();
                $("#save-change").prop('disabled', true);
            }
            else{
                $("#save-change").prop('disabled', false);
                $("#editQtyErr").hide();
            }
        });

        $('#productBox').on('click', '.product-box', function () {
            
            var childProQtyJson= $('.build_box_step_2').find("#childProQtyJson").val();
            const checkBuildbox = $(this).find("input[name='choose-buildbox']");
            checkBuildbox.prop("checked", true);
            $("#productBox").find("input[name='choose-buildbox']").each(function(){
                $(this).parent(".product-box").removeClass("selected");
            });
            if(checkBuildbox.prop('checked') == true){
               $(this).addClass("selected");
            }
            $('.build_box_step_3').find("#boxErrMsg").hide();
            const boxParentId = $(this).find("input[name='box_parent_Id']").val();
      
            $("#errMsg1").hide();
            $(".boxEdit").find("#editErrMsg-1").hide();
            var cartSelectedPro = $("input[name='cartSelectedPro']").val();
            $.ajax({
                type: "POST",
                url: url.build('buildbox/submit/proid'),
                data: {
                    boxId: $("input[name='choose-buildbox']:checked").val(),
                    boxParentId: boxParentId,
                    cartSelectedPro: cartSelectedPro,
                    selectKitProId: $('.build_box_step_2').find("#storeKitProId").val(),
                    storeKitItemQty: $('.build_box_step_2').find("#storeKitItemQty").val(),
                    childProQtyJson: childProQtyJson,
                    qtyforkit: $("#qtyforkit").val()
                },
                cache: false,
                dataType: 'html',
                showLoader: true,
                success: function (data) {
                    $("#respProId").html(data);
                    if($('#totalQty').length != 0 && $('#totalQty').val() != 0){
                        $("#submit").prop('disabled', true);
                    }
                    getFileVal2();
                    showKitQty();
                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                }
            });
        });

        var countBoxPro = 0;
        var countEditBoxPro = 0;
        //click box than select radio button
        $(".product-list").on('click', function (e) {
            var checkbox = $(this).children('input[type="checkbox"]');
            checkbox.prop('checked', !checkbox.prop('checked'));

            if (checkbox.prop('checked') == true) {
                $(this).addClass("selected");

                countBoxPro += checkbox.filter(':checked').length;
                countEditBoxPro += checkbox.filter(':checked').length;
                var qtyEachBox = $(this).find(".qtyEachBox").val(2);
                $("#errMsg").css("display", "none");
                $("#editErrMsg-1").hide();
                qtyEachBox = $(this).find("#qtyEachBox").val();
                boxQty = $("#boxQty").val();
                prodCartQty = $(this).find("#prodCartQty").val();
                totQty = qtyEachBox * boxQty;
                res = prodCartQty - totQty;
                if (res <= 0) {
                    $(this).find("#prodCartQty").val(0);
                    $(this).find("#err").text("you add" + parseInt(res) + "product");
                } else {
                    $(this).find("#prodCartQty").val(res);
                }
                $(this).find('.eachQty').hide();
                $(this).find('.qty-input').val(1);
            } else {
                $(this).find('.qty-input').val(0);
                $(this).removeClass("selected");
                countBoxPro -= 1;
                countEditBoxPro -=1;
                $(this).find(".qtyEachBox").val(0);
                var prodCartQtyHid = $(this).find("#prodCartQtyHid").val();
                $(this).find("#prodCartQty").val(prodCartQtyHid);
            }
            $(".pro-select").html(countBoxPro);
            $(".edit_build_box_1").find(".pro-select").html(countEditBoxPro);
            $("#count-product-box").html(`Selected (${countBoxPro}) product In a box`);
            $("#countProduct").html(`Selected (${countBoxPro}) product In a box`);
            e.preventDefault();
        });

        $(".pre-btn2").on('click', function () {
            $(".build_box_step_2").hide();
            $(".build_box_step_1").show();
        });

        $(".next-btn3").on('click', function () {
           
            var isValid = $("input[name='choose-buildbox']").is(":checked");
            var boxQty = $("#boxQty").val();
            if (isValid == true) {
                $(".build_box_step_3").hide();
                $("boxErrMsg").css("display", "none");
                $(".build_box_step_4").show();
                var box_price = $("#box_price").val()
                var totPrice = box_price * boxQty;
                $("#parPrice").text(box_price);
                $("#totPrice").text(totPrice);
            }
            else {
                $(".next-btn3").attr('disabled', false);
                $("#boxErrMsg").css("display", "block");
                // $("#colorError")[0].style.display = isValid ? "none" : "block";
            }
        });

        // Prev section show after click prev button 
        $(".pre-btn3").on('click', function () {
            $("#respOption").html("");
            $("#respProId").html("");
            $("#editRespOption").html('');
            $(".build_box_step_3").hide();
            $(".build_box_step_2").show();
        });
        $(".pre-btn4").on('click', function () {
            $("#respOption").html("");
            // $("#respProId").html("");
            $(".build_box_step_4").hide();
            $(".build_box_step_3").show();
        });

        $(".pre-btn5").on('click', function () {
            $(".build_box_step_4").show();
            $(".build_box_step_5").hide();
        });
        // end prev button script

        $(".next-btn4").on('click', function () {
            $(".build_box_step_4").hide();
            $(".build_box_step_5").show();
        });
        
        // $(".boxQty").keyup(function (e) {
        //     var qtyValue = $(this).val();
        //     var inputQty = ($("#inputProductQty").attr("inputProductQty"));
        //     if(parseInt(qtyValue) > parseInt(inputQty)){
               
        //         $("#qtyResErr").css({"color":"red","display":"block"});
        //         $("#qtyResErr").html("The requested qty exceeds the maximum qty allowed in shopping cart");
        //         $(".submit_button").find("#submit").prop('disabled', true); 
        //     }else{
        //         $(".submit_button").find("#submit").prop('disabled', false);
        //         $("#qtyResErr").css("display","none");
        //     }
        //     var boxQty = $(this).val();
        //     if(parseInt(boxQty) >= productLogoQty){
        //        $("#respProId").find(".additional:nth-child(4)").show();
        //        $("#respOption").find(".additional:nth-child(4)").show();
        //     }else{
        //         $("#respProId").find(".additional:nth-child(4)").hide();
        //         $("#respOption").find(".additional:nth-child(4)").hide();
        //     }
            
        //     if ($(this).val() < minProQty) {
        //         $("#minQty").html(minProQty);
        //         $("#QtyErr").show();

        //         $("#submit").prop('disabled', true);
        //     } else {
        //         $("#QtyErr").hide();
        //         $("#submit").prop('disabled', false);
        //         var box_price = $("#box_price").val();
        //         $("#per_box_price").val(box_price);
        //         var getBoxQty = url.build('buildbox/submit/getboxqty');
        //         var producQty = $('.proDimVal').attr('prod-qty');               
        //     }
        //     e.preventDefault(e);
        // });

        // after click submit button product is add to cart 
        $("#submit").click(function (e) {
            e.preventDefault(e);          
            var SelectedStoreQty = [];          
            var i=0;
            $("#respProId").find(".kitProQty").each(function() {
                //add only if the value is number
                if(this.value != 0){
                    SelectedStoreQty[i++] = this.value;
                }                            
            });

           $("#SelectedStoreQty").val(SelectedStoreQty);
           if($("#respProId").find("#totalQty").val() != 0){
                var isValid = $("#respProId").find("input[name='child_product']");
                if (isValid.is(":checked") != true) {
                    $(".submit_button").find("#selectError1").show();
                } else {
                    var form = $('#build_box_form')[0];
                    var data = new FormData(form);
                    $("#submit").prop("disabled", true);
                    $.ajax({
                        enctype: 'multipart/form-data',
                        type: "POST",
                        url: url.build('buildbox/submit/index'),
                        data: data,
                        processData: false,
                        contentType: false,
                        cache: false,
                        dataType: 'json',
                        showLoader: true,

                        success: function (data) {
                            if (data.success == "true") {
                                $("#submit").prop("disabled", true);
                                $(".pre-btn4").attr("disabled","disabled");
                                location.reload();                                
                                hideOptionFun();
                          
                            }
                        },
                        error: function (xhr, status, errorThrown) {
                            console.log('Error happens. Try again.');
                            $("#submit").prop("disabled", false);
                        }
                    });
                }
                if (isValid.length == 0) {
                    $(".submit_button").find("#selectError1").hide();
                    var form = $('#build_box_form')[0];
                    var data = new FormData(form);
                    $("#submit").prop("disabled", true);
                    $.ajax({
                        enctype: 'multipart/form-data',
                        type: "POST",
                        url: url.build('buildbox/submit/index'),
                        data: data,
                        processData: false,
                        contentType: false,
                        cache: false,
                        dataType: 'json',
                        showLoader: true,

                        success: function (data) {
                            if (data.success == "true") {
                                $("#pre-btn4").prop("disabled", true);
                                $(".pre-btn4").attr("disabled","disabled");
                                location.reload();                             
                                
                            }
                        },
                        error: function (xhr, status, errorThrown) {
                            console.log('Error happens. Try again.');
                            $("#submit").prop("disabled", false);
                        }
                    });
                    $("#proQty").hide();
                }
            }else{
                $("#proQty").show();

            }
        });

        // After choose color option 
        $('#respProId').on('change', '.childProOption', function () {
            let qtyforkit = $(".build_box_step_3").find("#qtyforkit").val();
            
            isValid = $("#respProId").find("input[name='child_product']").is(":checked");
            if (isValid == true) {
                $("#submit").prop("disabled", false);
                $(".submit_button").find("#selectError1").hide();
                let childOpId = $(this).val();
                $("#respOption").html('');
                $.ajax({
                    type: "POST",
                    url: url.build('buildbox/submit/getproductoption'),
                    data: {
                        childOpId: childOpId,
                        qtyforkit: qtyforkit
                    },
                    cache: false,
                    dataType: 'html',
                    showLoader: true,
                    success: function (data) {
                        $("#respOption").html(data);
                        getFileVal();
                        $("#submit").prop('disabled', false);
                    },
                    error: function (xhr, status, errorThrown) {
                        console.log('Error happens. Try again.');
                    }
                });
            }
        });

        /****
         ** Name Edit script
         ****/
        modal(options, $('.boxNameEdit'));
    
        $(".editName").each(function (){
			$(this).click(function (e) {
				$(this).hover();
				$(".del_popup").find("#continue-button").removeClass("editContinueBtn");
				$(".del_popup").find("#continue-button").removeClass("continue-button");
				$(".del_popup").find("#continue-button").addClass("editContName");
				
				let boxname = $(this).find(".edit-box-name-proid").attr("boxTitle");
				let proid = $(this).find(".edit-box-name-proid").val();
				let prodItemId = $(this).find(".edit-box-name-proid").attr("prodItemId");
				$(".edit-box-name").val(boxname);
				$(".edit-proid").val(proid);
				$(".edit-prodItemId").val(prodItemId);  
				$(".boxNameEdit").modal("openModal");
				e.preventDefault(e);
			});	
		});

        // After changes save name after click submit button 
        $("#build_box_edit").submit(function (e) {
            let nameEditProdUrl = url.build('buildbox/boxedit/boxnameedit');
            $.ajax({
                type: "POST",
                url: nameEditProdUrl,
                data: $("#build_box_edit").serialize(),
                cache: false,
                dataType: 'json',
                processData: false,
                showLoader: true,
                success: function (data) {
                    if (data.success == "true") {
                        location.reload();
                    }
                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                }
            });
            e.preventDefault(e);
        });

                        /****************************
                         *** Edit buildbox script ***
                         ****************************/

        var editMinQty = '';
        modal(options, $('#edit-box'));
        // Show edit popup
        $(".box-edit").on('click', function (e) {
            var editProId = $(this).find(".editProId").val();
            var proditemid = $(this).find(".editProId").attr("proditemid");
            var boxId = $(this).find(".boxId").val();
            var boxDimentions = $(this).find(".boxDimentions").val();
            editProId = $(this).find(".editProId").val();
            editItemId = $(this).find(".editProId").attr("prodItemId");
            $(".existId").val(editProId);
            $(".editItemId").val(editItemId);
            var boxName = $(this).find(".boxName").val();
            var boxId = $(this).find(".boxId").val();
            $(".existBoxName").val(boxName);
            $(".edit_build_box_3").find(".box-title-name").html(boxName);
            $.ajax({
                type: "post",
                url: url.build('buildbox/boxedit/editproid'),
                data: ({
                    editProId: editProId,
                    proditemid: proditemid,
                    boxId: boxId,
                    boxDimentions: boxDimentions
                }),
                cache: false,
                showLoader: true,
                success: function (data) {
            
                    $("#edit-box").modal("openModal");
                    $("#editRespSection").html(data);
                   
                    $(".edit_build_box_1").addClass("active");
                     $(".del_popup").find("#continue-button").removeClass("continue-button");
                    $(".del_popup").find("#continue-button").removeClass("editContName");
                    $(".del_popup").find("#continue-button").addClass("editContinueBtn");
                   				
                    editRespsection();
                    validateQty();
                },
                error: function (xhr, status, error) {
                    console.error(xhr);
                }
            });
        });
       
        $(".edit-next1").on('click', function (e) {
            var proDim = [];
            var prodQty = '';
            var editProQty = [];
            var selectedProId ='';
            var storeProId=[];
            var proIdKit ={};
            var checkNo = '';
            var checkVal = 0;
            $("#editRespSection").find('.product-list').each(function () {
                //get values
                cartProduId = '';
                if ($(this).find('.proDimVal').prop('checked') == true) {  
                    selectedProId = $(this).find('input[name="getItem[]"]').val();
                    storeProId.push(selectedProId);

                    let productQty = $(this).find("#prod-qty").val();
                    editProQty.push(productQty);
                    cartProduId = $(this).find('.proDimVal').attr('data-pro-id');
                    var proDimVal = $(this).find('.proDimVal').attr('data-dim');
                    prodQty = $(this).find('.proDimVal').attr('prod-qty');

                    $(".boxEdit").find("#editErrMsg-1").hide();
                    checkNo = 1;

                    var qtyInput = $(this).find('.qty-input').val();
                    var prodidforbox = $(this).find('.qty-input').attr("prodidforbox");
                    if(qtyInput == 0){
                        checkNo=0;
                        if(qtyMsg =='yes' || checkNo==0 ){
                            qtyMsg='no';
                            checkVal=1;
                            
                            $(this).find('.eachQty').show();                                             
                        }
                    }else {        
                        proIdKit[prodidforbox] = qtyInput;                                                      
                        $(this).find('.eachQty').hide();                        
                    }

                    proDim.push(proDimVal);
                }
                else {     
                    checkNo = $(".edit_build_box_1").find('input[type="checkbox"]:checked').length;
                    if (checkNo == 0) {
                        $(".boxEdit").find("#editErrMsg-1").show();
                    }
                }
            });


            // validate each product in edit product qty field 
            $(".edit_build_box_1").find(".qty-input").keyup(function(){
                var regex = /^\d*[.]?\d*$/;
                var qtyforbox = $(this).val();               
                if(!regex.test(qtyforbox)){
                    qtyforbox=''
                    return false;
                }
            }) 

            editMinQty = Math.min.apply(Math,editProQty);
            $("#editBoxQty").val(editMinQty);
            if (checkNo != 0 && checkVal != 1) {
                $('.edit_build_box_2').find('input[name="cartSelectedPro"]').val(storeProId);
                $.ajax({
                    type: "post",
                    url: url.build('buildbox/submit/dimensionvalue'),
                    data: ({
                        proDim: proDim,
                        cartProduId: cartProduId,
                        prodQty: prodQty,
                        selectedProId: storeProId,
                        proIdKit: proIdKit
                    }),
                    cache: false,
                    showLoader: true,
                    success: function (data) {
                        $("#editResponse").html(data);
                        $(".edit_build_box_1").hide();
                        $(".edit_build_box_2").show();
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr);
                    }
                });
            }
            e.preventDefault();
        });

        
        function editRespsection(){
            $("#editRespSection").find('.product-list').each(function () {      ;
                if ($(this).find('.proDimVal').prop('checked') == true) { 
                    $(this).find('.qty-input').val(1);
                    $(this).addClass("selected");
                }
                else {     
                    $(this).removeClass("selected");              
                    $(this).find('.qty-input').val(0);
                }
            });

			 $(".edit_build_box").find(".pro-select").html($('input:checkbox:checked').length);
			var countBoxPro = $('input:checkbox:checked').length;
			var countEditBoxPro = $('input:checkbox:checked').length;
			$(".edit_build_box").find("#countProduct").html(`Selected (${countBoxPro}) product In a box`);
			//click box than select radio button
			 $("#editRespSection").find(".product-list").on('click', function (e) {
				var checkbox = $(this).children('input[type="checkbox"]');
				checkbox.prop('checked', !checkbox.prop('checked'));

				if (checkbox.prop('checked') == true) {
					$(this).addClass("selected");

					countBoxPro += checkbox.filter(':checked').length;
					countEditBoxPro += checkbox.filter(':checked').length;
      
					var qtyEachBox = $(this).find(".qtyEachBox").val(2);
					$("#errMsg").css("display", "none");
					$("#editErrMsg-1").hide();
					qtyEachBox = $(this).find("#qtyEachBox").val();
					boxQty = $("#boxQty").val();
					prodCartQty = $(this).find("#prodCartQty").val();
					totQty = qtyEachBox * boxQty;
					res = prodCartQty - totQty;
					if (res <= 0) {
						$(this).find("#prodCartQty").val(0);
						$(this).find("#err").text("you add" + parseInt(res) + "product");
					} else {
						$(this).find("#prodCartQty").val(res);
					}

                    // if($("#editRespSection").find(".product-list").has(".selected")){
                        $(this).find('.qty-input').val(1);
                    // }

				} else {
                    $(this).find('.qty-input').val(0);
					$(this).removeClass("selected");
					if(countBoxPro == 1){
					  countBoxPro -= 1;
					  countEditBoxPro -=1;
				    }else{
					  countBoxPro -= 1;
					  countEditBoxPro -=1;
					}
					$(this).find(".qtyEachBox").val(0);
					var prodCartQtyHid = $(this).find("#prodCartQtyHid").val();
					$(this).find("#prodCartQty").val(prodCartQtyHid);
				}
				
				$(".pro-select").html(countBoxPro);
				$(".edit_build_box_1").find(".pro-select").html(countEditBoxPro);
				$("#count-product-box").html(`Selected (${countBoxPro}) product In a box`);
				$(".edit_build_box").find("#countProduct").html(`Selected (${countBoxPro}) product In a box`);
				e.preventDefault();
			});
			
		}

        $('#editResponse').on('click', '.product-box', function () {
            var childProQtyJson= $('#editRespSection').find("#childProQtyJson").val()
            const checkBuildbox = $(this).find("input[name='choose-buildbox']");

            checkBuildbox.prop("checked", true);
            $("#editResponse").find("input[name='choose-buildbox']").each(function(){
                $(this).parent(".product-box").removeClass("selected");
            });
            if(checkBuildbox.prop('checked') == true){
               $(this).addClass("selected");
            }

            const submitProdUrl = url.build('buildbox/submit/proid');
            const boxParentId = $(this).find("input[name='box_parent_Id']").val();
            var cartSelectedPro = $('.edit_build_box_2').find("input[name='cartSelectedPro']").val();
           
            $("#editBoxErrMsg").css("display", "none");
            $.ajax({
                type: "POST",
                url: submitProdUrl,
                data: {
                    boxId: $("input[name='choose-buildbox']:checked").val(),
                    cartSelectedPro: cartSelectedPro,
                    boxParentId: boxParentId,
                    selectKitProId: $('#editRespSection').find("#storeKitProId").val(),
                    storeKitItemQty: $('#editRespSection').find("#storeKitItemQty").val(),
                    childProQtyJson: childProQtyJson,
                    qtyforkit: $("#editResponse").find("#qtyforkit").val()
                },
                cache: false,
                dataType: 'html',
                showLoader: true,
                success: function (data) {
                    $("#respProDetails").html(data);
                    getEditFileVal2();
                    showEditKitQty();
                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                }
            });
        });

        $(".edit-next2").click(function () {
            var isValid = $("#editResponse").find("input[name='choose-buildbox']").is(":checked");
            if (isValid == true) {
                $(".edit_build_box_2").hide();
                $("#editBoxErrMsg").css("display", "none");
                $(".edit_build_box_3").show();
                var box_price = $("#box_price").val();
                var editBoxQty = $("#editBoxQty").val();
                var totPrice = box_price * editBoxQty;
                $("#editparPrice").text(box_price);
                $("#editPrice").text(totPrice);
            }
            else {
                $(".next-btn3").attr('disabled', false);
                $("#editBoxErrMsg").css("display", "block");
                $("#colorError")[0].style.display = isValid ? "none" : "block";
            }
        })
        $("#save-change").click(function (e) {
            e.preventDefault(e);
            var SelectedStoreQty = [];          
            var i=0;
            $("#respProDetails").find(".kitProQty").each(function() {
                //add only if the value is number
                if(this.value != 0){
                    SelectedStoreQty[i++] = this.value;
                }                            
            });

           $("#StoreEditProQty").val(SelectedStoreQty);
           if($("#respProDetails").find("#totalQty").val() != 0){
                var isValid = $("#respProDetails").find("input[name='child_product']");
                if (isValid.length != 0) {
                    if (isValid.is(":checked") != true) {
                        $(".edit_submit_button").find("#selectError").show();
                    } else {
                        var form = $('#editBoxProdct')[0];
                        $(".edit_submit_button").find("#selectError").show();
                        // Create an FormData object 
                        var data = new FormData(form);
                        $("#save-change").prop("disabled", true);
                        $.ajax({
                            enctype: 'multipart/form-data',
                            type: "POST",
                            url: url.build('buildbox/boxedit/editboxsave'),
                            data: data,
                            processData: false,
                            contentType: false,
                            cache: false,
                            dataType: 'json',
                            showLoader: true,
                            success: function (data) {
                                if (data.success == "true") {
                                    location.reload();
                                    $("#save-change").prop("disabled", true);
                                    $(".edit_build_box_3").find(".pre-btn3").attr("disabled","disabled");
                                }
                            },
                            error: function (xhr, status, errorThrown) {
                                console.log('Error happens. Try again.');
                                $("#submit").prop("disabled", false);
                            }
                        });
                    }
                } else {
                    $(".edit_submit_button").find("#selectError").hide();
                    var form = $('#editBoxProdct')[0];
                    $(".edit_submit_button").find("#selectError").show();
                    // Create an FormData object 
                    var data = new FormData(form);
                    
                    $("#save-change").prop("disabled", true);
                    $.ajax({
                        enctype: 'multipart/form-data',
                        type: "POST",
                        url: url.build('buildbox/boxedit/editboxsave'),
                        data: data,
                        processData: false,
                        contentType: false,
                        cache: false,
                        dataType: 'json',
                        showLoader: true,
                        success: function (data) {
                            if (data.success == "true") {
                                location.reload();
                                $("#save-change").prop("disabled", true);
                                $(".edit_build_box_3").find(".pre-btn3").attr("disabled","disabled");
                            }
                        },
                        error: function (xhr, status, errorThrown) {
                            console.log('Error happens. Try again.');
                            $("#submit").prop("disabled", false);
                        }
                    });
                   
                    $("#editProQty").hide();
                }
            }else{

                $("#editProQty").show();
            }
        });

        $(".edit_build_box_2").find(".edit-pre1").click(function () {
            $("#editBoxProdct").find(".edit_build_box_2").hide();
            $("#editBoxProdct").find(".edit_build_box_1").show();
        });
        $(".edit_build_box_3").find(".pre-btn3").click(function () {
            $("#editBoxProdct").find(".edit_build_box_3").hide();
            $("#editBoxProdct").find(".edit_build_box_2").show();
        });

        $('#respProDetails').on('change', '.childProOption', function () {
            const qtyforkit = $(".edit_build_box_3").find("#qtyforkit").val();

            let childOpId = $(this).val();
            $(".edit_submit_button").find("#selectError").hide();
            $("#editRespOption").html('');
            $.ajax({               
                type: "POST",
                url: url.build('buildbox/submit/getproductoption'),
                data: {
                    childOpId: childOpId,
                    qtyforkit: qtyforkit
                },
                cache: false,
                dataType: 'html',
                showLoader: true,
                success: function (data) {
                    $("#editRespOption").html(data);
                    getEditFileVal();
                    $("#submit").prop('disabled', false);

                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                }
            });
        });
        $(".action-close").click(function(){
            location.reload();
        });
               
        function getFileVal(){    
            var productBoxQty = $("#boxQty").val();
            
            if(productBoxQty <= productLogoQty){
               $(".addons-section").find(".additional:nth-child(4)").hide();
            }       
            $("#respOption").find('input[type="file"]').change(function(e){
				
                var addonsPrice=$(this).attr("addonsPrice");
                var fileName = e.target.files[0].name;
                $("#respOption").find("#storeAddonsPrice").val(addonsPrice);
				$(this).next('[name="radioSelect"]').prop("checked", false);
                // $(".addFileName").html('');
                // $(this).before(`<p class="addFileName">${fileName}</p>`);
				var a = $(this).next('[name="radioSelect"]');

				a.prop("checked", true);
				var parentClass = $(this).parent().attr("class");
				
				$("#respOption").find("."+parentClass).each(function(){
					if($(this).find('[name="radioSelect"]').is(':checked')==false){
						$(this).find('input[type="file"]').val("");
						
					}else{
					  var fileName = e.target.files[0].name;
						var allowedExtensions =/(\.jpg|\.png|\.gif)$/i;

						if (!allowedExtensions.exec(fileName)) {
							$("#submit").prop('disabled', true);
							$(this).next('[name="radioSelect"]').prop("checked", false);
							$("#fileErr").show();
                            $("#respOption").find(".addFileName").css("color","red");
							fileName.value = '';
							return false;
						}else{
                            $("#respOption").find(".addFileName").css("color","black");
							$("#fileErr").hide();
							$("#submit").prop('disabled', false);
						}
					}
				});
            });
        }

        function getFileVal2(){
             var productBoxQty = $("#boxQty").val();
            
            if(productBoxQty <= productLogoQty){
               $(".addons-section").find(".additional:nth-child(4)").hide();
            }
            $("#respProId").find('input[type="file"]').change(function(e){

                var addonsPrice=$(this).attr("addonsPrice");
                var fileName = e.target.files[0].name;
                // $("#respProId").find("#addFileName").html('');
                // $(this).before(`<p id="addFileName">${fileName}</p>`);
                // alert(fileName + ' is the selected file .');
                $("#respProId").find("#storeAddonsPrice").val(addonsPrice);

				$(this).next('[name="radioSelect"]').prop("checked", false);
				var a = $(this).next('[name="radioSelect"]');
				a.prop("checked", true);
				var parentClass = $(this).parent().attr("class");
							
				$("#respProId").find("."+parentClass).each(function(){
					if ($(this).find('[name="radioSelect"]').is(':checked')==false){
						
						$(this).find('input[type="file"]').val("");
					} else {
                       $option_id = $(this).find('input[type="file"]').attr("optionId");
                       $("#option_id").val($option_id);
						var fileName = e.target.files[0].name;
						var allowedExtensions =/(\.jpg|\.png|\.gif)$/i;
                       
						if (!allowedExtensions.exec(fileName)) {
							$("#submit").prop('disabled', true);
							$(this).next('[name="radioSelect"]').prop("checked", false);
							$("#fileErr").show();
                            $("#addFileName").css("color","red");
							fileName.value = '';
							return false;
						}else{
                            $("#addFileName").css("color","black");
							$("#fileErr").hide();
							$("#submit").prop('disabled', false);
						}
					}
				});
            });
        }
        
        function getEditFileVal2(){
                var productBoxQty = $("#editBoxQty").val();
                if(productBoxQty <= productLogoQty){
                  $("#respProDetails").find(".additional:nth-child(4)").hide();
                }

				$("#editFileErr").hide();
				$("#respProDetails").find('input[type="file"]').change(function(e){

                    var addonsPrice= $(this).attr("addonsPrice");
                    var fileName = e.target.files[0].name;
                    // alert(fileName + ' is the selected file .');
                    $("#respProDetails").find("#storeAddonsPrice").val(addonsPrice);

                    // $("#editFileName").html('');
                    // $(this).before(`<p id="editFileName">${fileName}</p>`);

					$(this).next('[name="radioSelect"]').prop("checked", false);
					var a = $(this).next('[name="radioSelect"]');					
					a.prop("checked", true);
					var parentClass = $(this).parent().attr("class");
					
					$("#respProDetails").find("."+parentClass).each(function(){
						if($(this).find('[name="radioSelect"]').is(':checked')==false){
							
							$(this).find('input[type="file"]').val("");
						} else {

							var fileName = e.target.files[0].name;
							var allowedExtensions =/(\.jpg|\.png|\.gif)$/i;
							if (!allowedExtensions.exec(fileName)) {

								$("#save-change").prop('disabled', true);
								$(this).next('[name="radioSelect"]').prop("checked", false);
								$("#editFileErr").show();
                                $("#editFileName").css("color","red");
								fileName.value = '';								
								return false;
							}else{
                                $("#editFileName").css("color","black");
								$("#editFileErr").hide();
								$("#save-change").prop('disabled', false);
							}
						}
					});
                
				});
		}
		
		function getEditFileVal(){
            var productBoxQty = $("#editBoxQty").val();
            if(productBoxQty <= productLogoQty){
               $("#editRespOption").find(".additional:nth-child(4)").hide();
            }
				$("#editRespOption").find('input[type="file"]').change(function(e){
					
					$(this).next('[name="radioSelect"]').prop("checked", false);
					var a = $(this).next('[name="radioSelect"]');
					a.prop("checked", true);
					var parentClass = $(this).parent().attr("class");
					
					$("#editRespOption").find("."+parentClass).each(function(){
						
						if($(this).find('[name="radioSelect"]').is(':checked')==false){
							
							$(this).find('input[type="file"]').val("");							
						} else {
							
							var fileName = e.target.files[0].name;
                            // $(".editFileName").html('');
                            // $(this).before(`<p class="editFileName">${fileName}</p>`);                            
							var allowedExtensions =/(\.jpg|\.png|\.gif)$/i;
							if (!allowedExtensions.exec(fileName)) {
								
								$("#save-change").prop('disabled', true);
								$(this).next('[name="radioSelect"]').prop("checked", false);
								$("#editFileErr").show();
								fileName.value = '';	
                                // $(".editFileName").css("color","red");
								return false;
							} else {
								// $(".editFileName").css("color","black");
								$("#editFileErr").hide();
								$("#save-change").prop('disabled', false);
							}
						}
					});
                
				});
		}
		
        function showKitQty(){

            calculateTotQty();
            $(".qtyNotValide").hide();
            var selectedItemId = $(".build_box_step_3").find('#cartSelectedPro').val();
            var selectedProductId = [];
            $(".build_box_step_2").find('.proDimVal').each(function(){
                selectedProductId.push($(this).attr("data-pro-id"));
            });
            console.log(selectedProductId,  'selectedProductId');
            // alert(selectedProductId);
            var thisEvent = '';
            $("#respProId").find(".kitProQty").on("keyup", function() {
                // var totalQty = $("#respProId").find("#totalQty").val();
                var totalQty = kitConfQtyvalid();
                $("#proQty").hide();
                $(".qtyNotValide").hide();
                $("#respProId").find("#qty-error").html("");
                thisEvent = $(this);
                $.ajax({
                    type: "post",
                    url: url.build('buildbox/submit/checkproductqty'),
                    data: {
                        qtyVal: this.value,
                        kitconfproid: $(this).attr("kitconfproid"),
                        selectedItemId: selectedItemId,
                        totalQty: totalQty,
                        selectedProductId: selectedProductId
                    },
                    cache: false,
                    showLoader: true,
                    success: function (data) { 
                        $(".qtyNotValide").hide();
                        if(data.success != "true"){
                            CheckAndShowErr(true);
                            $("#totalQty").css({'border':'2px solid red'});
                            $(".qtyNotValide").show();
                            $(".qtyNotValide").html(data);
                            $("#respProId").find("#qty-error").show();
                            $("#submit").prop('disabled', true);
                        }else{
                            CheckAndShowErr(false);
                            $("#respProId").find("#qty-error").hide();
                            $("#submit").prop('disabled', false);
                            $("#totalQty").css({'border':'2px solid black'});
                            $("#respProId").find(".invalidQty").css("border","2px solid black");
                            $(".qtyNotValide").hide();
                        }
                    }
                })
                calculateTotQty();
                function CheckAndShowErr(a){
                    if(a == true){
                        // 
                        proqtyinput();
                        $(thisEvent).addClass("custom_invalidQty");
                        
                        if($(thisEvent).hasClass("custom_invalidQty")){
                            $(thisEvent).attr('readonly', false);
                        }

                                                
                        $("#respProId").find(".custom_invalidQty").css("border","2px solid red");
                        $(thisEvent).after("<p id='qty-error'>Product qty is not available!</p>");
                    }
                }
            });

            function proqtyinput(){
                $("#respProId").find(".kitProQty").each(function() {
                    $(this).attr('readonly', true);
                    
                })
            }
            $('#totalQty').attr('readonly', true);  
           
            function calculateTotQty() {
                var storeKitItemQty = $("#storeKitItemQty").val();
                var QtyObj = {};
                
                var sumQty = 0;
                //iterate through each textboxes and add the values
                $("#respProId").find(".kitProQty").each(function() {
                   
					var invalidQty = $("#invalidQty").val();
                    var proQty = $("#invalidQty").attr("productqty");
                    console.log($(this)<= proQty);
                    if($(this)<= proQty){
                        $(".qtyNotValide").show();
                        $(this).css("background-color", "yellow");              
                    }else{    
                        $(this).css("background-color", "red");
                        $(".qtyNotValide").show();
                    }
                    //add only if the value is number                    
                    if (!isNaN($(this).val()) && $(this).val().length != 0) {
                        sumQty += parseInt(this.value);                        
                        $(this).css("background-color", "#FEFFB0");
						if($(this).val() != 0){
							var KitConfProId = $(this).attr("KitConfProId");
							QtyObj[KitConfProId] = $(this).val();
							
						}
                    }
                    else if ($(this).val().length != 0){                       
                        $(this).css("background-color", "red");
                    }
                    
                });
                
				var jsonQty= JSON.stringify(QtyObj);
				$("#kitConfChildQtyId").val(jsonQty);
                
                $('#totalQty').val(sumQty);
            } 

            function kitConfQtyvalid(){    
                var kitAllQty = 0;           
                $("#respProId").find(".kitProQty").each(function() {        
                    kitAllQty = kitAllQty + parseInt($(this).val());                    
                });

                return kitAllQty;
            }          
        }
        
        /**
         * Edit Kit qty of product section script
         */

        function showEditKitQty(){
            calculateTotQty();
            var selectedItemId = $(".edit_build_box_2").find('#cartSelectedPro').val();
            $("#respProDetails").find(".kitProQty").on("keyup", function() {
                var totalQty = editKitConfQtyvalid();
                $("#editProQty").hide();
                $.ajax({
                    type: "post",
                    url: url.build('buildbox/submit/checkproductqty'),
                    data: {
                        qtyVal: this.value,
                        kitconfproid: $(this).attr("kitconfproid"),
                        selectedItemId: selectedItemId,
                        totalQty: totalQty
                    },
                    cache: false,
                    showLoader: true,
                    success: function (data) {
                        if(data.success != "true"){
                            $("#respProDetails").find("#totalQty").css({'border':'2px solid red'});
                            $(".edit_build_box_3").find(".qtyNotValide").show();
                            $(".edit_build_box_3").find(".qtyNotValide").html(data);
                            $("#save-change").prop('disabled', true);
                        }else{
                            $("#save-change").prop('disabled', false);
                            $("#respProDetails").find("#totalQty").css({'border':'2px solid black'});
                            $(".edit_build_box_3").find(".qtyNotValide").hide();
                        }
                    }
                })
                calculateTotQty();
            });

            $('#respProDetails').find('#totalQty').attr('readonly', true);  

            function calculateTotQty() {
                var storeKitItemQty = $("#storeKitItemQty").val();
                var QtyObj = {};
                
                var sumQty = 0;
                //iterate through each textboxes and add the values
                $("#respProDetails").find(".kitProQty").each(function() {
					
                    //add only if the value is number                    
                    if (!isNaN($(this).val()) && $(this).val().length != 0) {
                        sumQty += parseInt(this.value);                        
                        $(this).css("background-color", "#FEFFB0");
						if($(this).val() != 0){
							var KitConfProId = $(this).attr("KitConfProId");
							QtyObj[KitConfProId] = $(this).val();
							
						}
                    }
                    else if ($(this).val().length != 0){
                       
                        $(this).css("background-color", "red");
                    }
                    
                });
				var jsonQty= JSON.stringify(QtyObj);
				$('#respProDetails').find("#kitConfChildQtyId").val(jsonQty);
                $('#respProDetails').find('#totalQty').val(sumQty);            
            } 
            function editKitConfQtyvalid(){    
                var kitAllQty = 0;           
                $("#respProDetails").find(".kitProQty").each(function() {        
                    kitAllQty = kitAllQty + parseInt($(this).val());                    
                });

                return kitAllQty;
            }              
        }

        // show esdcclick section 
		$("#esdcclick").click(function(){
			$(".esdcinfo-show").toggle();
		 });
         function hideOptionFun(){
         $('.item-options').find("dt").each(function(){
            if($(this).html() == 'unique_box_id'){
                $(this).addClass("abc");
                $(this).next().addClass("abc2");
                $(this).hide();
                $(this).next().hide();
            }
        })
      }

        //   >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        $("#shopping-box-table").find(".removeBtn").each(function(){
            $(this).click(function(){
                var boxItemId = $(this).attr("boxItemId");

                var confBoxId= $("#confBoxId").attr("confBoxsId");
                $.ajax({
                    type: "post",
                    url: url.build('buildbox/remove/removefrombox'),
                    data: {
                    id: boxItemId,
                    confBoxId:confBoxId
                    },
                    cache: false,
                    showLoader: true,
                    success: function (data) {
                        location.reload();
                        console.log(data)                      
                    }
                })  
            })
        })
        $(".build_box_step_2").find(".prodQtysec").click(function (e) {
            e.preventDefault();
            e.stopPropagation();
        });

        $(".build_box_step_2").find(".qty-input").keypress(function (e) {
            
            var regex = /^\d*[.]?\d*$/;
			var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
			if (regex.test(str)) {
				return true;
			}
			else
			{
				e.preventDefault();
				return false;
			}
        });

        //Edit Qty validator
        function validateQty(){
            $(".edit_build_box_1").find(".prodQtysec").click(function (e) {
                e.preventDefault();
                e.stopPropagation();
            });
            $(".edit_build_box_1").find(".qty-input").keypress(function (e) {
                var regex = /^\d*[.]?\d*$/;
                var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);

                if (regex.test(str)) {
                    return true;
                }
                else
                {
                    e.preventDefault();
                    return false;
                }
            });

        }

        /////////calculate cart price 
        var priceFormat = {
            decimalSymbol: '.',
            groupLength: 3,
            groupSymbol: ",",
            integerRequired: false,
            pattern: "₹%s",
            precision: 2,
            requiredPrecision: 2
        };
              
        var confCartPrice = 0;
        var totCartQty = 0;
        $("#shopping-box-table").find(".configurable_box_section").each(function(){
            $(this).find(".cart_conf_prodprice_num").each(function(){
                confCartPrice  += parseInt($(this).attr("cartprice"));
            });

            $(this).find(".totBoxPrice").html(priceUtils.formatPrice(confCartPrice, priceFormat));

            
            $(this).find(".cartConfigQty").each(function(){
                totCartQty += parseInt($(this).attr("cartqty"));
            })
            
            $(this).find(".totKitQty2").html(totCartQty);
            
            $(this).find(".grandTotBoxPrice").html(priceUtils.formatPrice(confCartPrice * parseInt(totCartQty), priceFormat));
        })


        var simpleCartPrice = 0;
        var simpleCartQty = 0
        // alert($("#shopping-box-table").find(".simple_porduct_box_section").length);
        $("#shopping-box-table").find(".simple_porduct_box_section").each(function(){
            $(this).find(".simpleTotBoxPrice").html('0');
            $(this).find(".simpleGrandTotBoxPrice").html('0');
            simpleCartPrice=0;
            $(this).find(".cart_prodprice_num").each(function(){
                simpleCartPrice += parseInt($(this).attr("cartprice"));
            });

            simpleCartQty = $(this).find(".totKitQty").attr("cartqty");
            // alert(priceUtils.formatPrice(simpleCartPrice, priceFormat));
            $(this).find(".simpleTotBoxPrice").html(priceUtils.formatPrice(simpleCartPrice, priceFormat));
            $(this).find(".simpleGrandTotBoxPrice").html(priceUtils.formatPrice(simpleCartPrice * parseInt(simpleCartQty), priceFormat));
        });
    }


   
);
