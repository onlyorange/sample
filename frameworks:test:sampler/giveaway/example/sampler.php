<?php
	require_once("_config.php");
	$states_abbrev_arr = array( "AL", "AK", "AZ", "AR", "CA", "CO", "CT", "DC", "DE", "FL", "GA", "HI", "ID", "IL", "IN", "IA", "KS", "KY", "LA", "ME", "MD", "MA", "MI", "MN", "MS", "MO", "MT", "NE", "NV", "NH", "NJ", "NM", "NY", "NC", "ND", "OH", "OK", "OR", "PA", "RI", "SC", "SD", "TN", "TX", "UT", "VT", "VA", "WA", "WV", "WI", "WY");
?>
<!--- BEGIN Tab Content --->
<html>
	<head>
		<title>Sampler</title>
	</head>
	<body>
		
		<form id="sampler_entry_form">
			<ul>
				<li>	
					<span class="formlab">First Name</span>
					<input id="sampler_first_name" name="sampler_first_name" type="text" class="one_line_text" />
				</li>
				<li>	
					<span class="formlab">Last Name</span>
					<input id="sampler_last_name" name="sampler_last_name" type="text" class="one_line_text" />
				</li>
				<li>	
					<span class="formlab">Email Address</span>
					<input id="sampler_email_address" name="sampler_email_address" type="text" class="one_line_text" />
				</li>
				<li>	
					<span class="formlab">Street Address</span>
					<input id="sampler_addr" name="sampler_address" type="text" class="one_line_text" />
				</li>
				<li>
					<span class="formlab">City</span>
					<input id="sampler_city" name="sampler_city" type="text" class="one_line_text" />
					<select name="sampler_state" id="sampler_state">
						<option></option>
						<?php foreach($states_abbrev_arr as $state): ?>
						<option><?php echo $state; ?></option>
						<?php endforeach; ?>
					</select>
					<input id="sampler_zip" name="sampler_zip" type="text" class="one_line_text" />
				</li>
				<li class="clearfix">	
					<span class="formlab">Date of Birth</span>
					<div class="select_collection clearfix" id="sampler_dob">
						<select id="sampler_month" name="sampler_month">
						<?php
							$oldest_year = date("Y") - 18;
							$sweep_months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" );
							foreach($sweep_months as $m): ?>
							<option><?php echo $m; ?></option>
						<?php endforeach; ?>
						</select>
						<select id="sampler_day" name="sampler_day">
						<?php for($i = 1; $i < 32; $i++): ?>
							<option><?php echo $i; ?></option>		
						<?php endfor; ?>
						</select>
						<select id="sampler_year" name="sampler_year">
						<?php for($i = $oldest_year+1; $i > 1920; $i--): ?>
							<option><?php echo $i; ?></option>
						<?php endfor; ?>
						</select>
					</div>
				</li>
				<li class="clearfix">
					<div name="sampler_submit" id="sampler_submit">Submit</div>
				</li>
			</ul>
		</form>
	</body>
</html>
<script type="text/javascript"
$("#sampler_first_name").val("Caroline");
$("#sampler_last_name").val("Amaba");
$("#sampler_email_address").val("knilline@gmail.com");
$("#sampler_addr").val("703 Park Ave, Apt 10");
$("#sampler_city").val("Hoboken");
$("#sampler_state").val("NJ");
$("#sampler_zip").val("07030");
$("#sampler_month").val("February");
$("#sampler_day").val("26");
$("#sampler_year").val("1990");


function checkEntered() {
	if($.cookie('sampler_entered')) {
		
		$('#sampler_entry_form').fadeOut();
		$('.right_form').css('font-size', '14pt');
		$('.right_form').css('padding-top', '50px');
		$('.right_form').html("Sorry, you've already entered for a sample!");
	}
}

$(document).ready(function() {
	checkEntered();
});

$('#sampler_submit').click(function() {
	var first_name = $("#sampler_first_name").val();
	var last_name = $("#sampler_last_name").val();
	var email = $("#sampler_email_address").val();
	var address = $("#sampler_addr").val();
	var city = $("#sampler_city").val();
	var state = $("#sampler_state").val();
	var zip = $("#sampler_zip").val();
	var month = $("#sampler_month").val();
	var day = $("#sampler_day").val();
	var year = $("#sampler_year").val();
	var what_coffee = $("#sampler_brewer").val();
	
	if(!$('#sampler_submit').hasClass('disabled')) {
		if(!first_name) {
			alert("Please enter your first name.");
		} else if(!last_name) {
			alert("Please enter your last name.");
		} else if(email.indexOf("@") < 0) {
			alert("Please enter a valid email.");
		} else if(!address || !state || !city || !zip) {
			alert("Please complete your full mailing address.");
		} else if(!month || month == "MONTH"){
			alert("Please select a month.");
		} else if(day < 0 || day == "DAY"){
			alert("Please select a day.");
		} else if(year < 0 || year == "YEAR"){
			alert("Please select a year.");
		} else if(!what_coffee) {
			alert("Select which type of sample you want.");
		} else {
			$('#sampler_entry_form').fadeOut();
			$('.right_form').html('<img src="<?php echo CDN; ?>images/loading.gif" />');
			$.post("sampler_ajax.php",
				{
					sampler_first_name: first_name,
					sampler_last_name: last_name,
					sampler_email_address: email,
					sampler_address: address,
					sampler_city: city,
					sampler_state: state,
					sampler_zip: zip,
					sampler_month: month,
					sampler_day: day,
					sampler_year: year,
					sampler_brewer: what_coffee
				}, function(res) {
					switch(res) {
						case "already_entered":
							form_res = "Sorry, you've already entered for a sampler pack!";
							break;
						case "reached_limit":
							form_res = "Sorry, we're all out of sampler packs!";
							break;
						case "ok":
							form_res = "A Green Mountain Coffee&reg; Fair Trade coffee<br/>sampler pack is headed your way.";
							setSamplerCookie();
							break;
						default:
							form_res = "There was an error in your entry. Please double check all fields and <a href=\"sampler.php\" target=\"_self\">submit again</a>.";
							break;
					}
					$('.right_form').css('font-size', '14pt');
					$('.right_form').css('padding-top', '50px');
					$('.right_form').html(form_res);
				}
			);
		}
	}
});
</script>