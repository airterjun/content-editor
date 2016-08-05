<script type="text/javascript">
	var $$ = jQuery;

	var saved_data = <?php echo json_encode(get_post_meta($object->ID, 'complete_layout_data', true)); ?>;
	var currBoxEdit = null;


	$$(function () {
		var options = {
			cellHeight: 200,
			verticalMargin: 0,
			minWidth : 200,
			resizable: {
				handles: 'e, w'
			}
		};

		$$('.grid-stack').gridstack(options);

		$$("#complete_layout_data").val(saved_data);

		new function () {

			var ths = this;

			this.grid = $$('.grid-stack').data('gridstack');

			this.addNewWidget = function (type, modules) {
				this.grid.addWidget($$('<div class="grid-stack-item" data-box-type="'+type+'" data-module-name="'+modules+'"><div class="grid-stack-item-content" ><a href="#" class="edit-content">Edit</a></div></div>'), 0, 0 , 3, 1, true);
				return false;
			}.bind(this);


			this.saveGrid = function () {
				this.serializedData = _.map($$('.grid-stack > .grid-stack-item:visible'), function (el) {
					el = $$(el);

					var node = el.data('_gridstack_node');

					return {
						x           : node.x,
						y           : node.y,
						width       : node.width,
						height      : node.height,
						type        : el.attr('data-box-type'),
						content     : el.attr('data-content'),
						banner      : el.attr('data-banner'),
						url         : el.attr('data-link-url'),
						bgColor     : el.attr('data-bg-color'),
						attrClass   : el.attr('data-attr-class'),
						getPost     : el.attr('data-post-id'),
						shortCode   : el.attr('data-content-shortcode')

					};
				}, this);

				$$("#complete_layout_data").val(JSON.stringify(this.serializedData, null, ''));
//				console.log(JSON.stringify(this.serializedData, null, '    '));


				var ajax = $$.ajax({
					url : ajaxurl,
					type : 'post',
					data : {
						datas : ths.serializedData,
						action : 'data_parsing'
					}
				});


				ajax.success(function(data){

					$$("#content").val(data);
					
				});


				return false;

			}.bind(this);


			this.closeMenuBox = function(){

				$$('.select-box-type').fadeOut();

			};


			this.showMenuBox = function(){

				$$('.select-box-type').fadeIn();

			};


			$$(document).on('click', '.delete-content', function(){

				var el = $$(this).parent().parent();

				ths.grid.removeWidget(el);

				return false;

			});


			/*
			** Open modal grid type
			*/
			$$(document).on('click', '#add-new-box', function(){

				console.log(ths);
				ths.showMenuBox();
				return false;

			});


			/*
			** Select post type and create the block
			*/
			$$(document).on('click', 'li.type', function(){

				var type            = $$(this).attr('data-box-type');
				var modules_type    = $$(this).attr('data-module-type');

				ths.closeMenuBox();

				ths.addNewWidget(type, modules_type)

			});


			$$(document).on('click', '#save-content', function () {


				if( $$(this).attr('data-save-as') == 'shortcode'){
					update_shortcode(generate_shortcode());
					update_content();
				}else{
					update_content();
				}


				$$(".visual-editor").removeClass('active');

				return false;

			});


			function update_content(){

				currBoxEdit.attr("data-content", encodeURIComponent($$("#gridster_edit").val()));
				generate_data_attr();
				ths.saveGrid();


			}


			function update_shortcode(shortcode){
				currBoxEdit.attr("data-content-shortcode", encodeURIComponent(shortcode) );
				ths.saveGrid();
			}

		};


		$$(document).on('click', '.edit-content', function () {

			var $this = $$(this);
			var type = $this.parent().parent().attr('data-box-type');

			currBoxEdit = $this.parent().parent();

			var getData = tmp.get(type);


			/*
			 * Show content editor
			 */
			$$("#wp-gridster_edit-wrap").css('display','block');

			/*
			 * Disabled content editor if post_content
			 */
			if( type == 'post_content'){
				$$("#wp-gridster_edit-wrap").css('display','none');
			}

			getData.success(function(data){

				$$("#visual-editor .editor_container").html(data);

				edit_content(currBoxEdit);

				$$(".visual-editor").addClass('active');
			})

			return false;

		});


		function edit_content($this){
			var content = '', url = '',  attr_class = '',  banner = '', bg_color = '';

			if( $this.attr("data-content") ){
				content = decodeUri($this.attr("data-content"));
			}

			if( $this.attr("data-link-url") ){
				url = decodeUri($this.attr("data-link-url"));
			}

			if( $this.attr("data-attr-class") ){
				attr_class = decodeUri($this.attr("data-attr-class"));
			}


			if( $this.attr("data-banner") ){
				banner = decodeUri($this.attr("data-banner"));
			}


			if( $this.attr("data-bg-color") ){
				bg_color = decodeUri($this.attr("data-bg-color"));
			}

			$$("#gridster_edit").val(content);
			$$("#box-link-url").val(url);
			$$("#box-attr-class").val(attr_class);
			$$("#box-banner").val(banner);
			$$("#box-bg-color").val(bg_color);
		}



		function decodeUri(string){
			return decodeURIComponent((string+'').replace(/\+/g, '%20'));
		}


		function generate_shortcode(){

			var fields = $$('.field-editor');

			var shortcodeName = currBoxEdit.attr('data-box-type');

			var shortcode = '[' + shortcodeName + ' ';

			var t = 1;

			$$.each(fields, function(i,e){

				var key = $$(e).attr('id');
				var val = $$(e).val();

				if( t ==  fields.length){
					shortcode += key + '=' + val;
				}else{
					shortcode += key + '=' + val +' ';
				}

				console.log(t);

				t++;

			});

			shortcode += ']';


			return shortcode;

		}


		function generate_data_attr(){

			var fields = $$('.field-editor');

			var data = '';

			$$.each(fields, function(i,e){

				var key = $$(e).attr('id');
				var val = $$(e).val();

				data += currBoxEdit.attr(key.replace('box', 'data'),encodeURIComponent(val));
			});



			return data;

		}
	});
</script>