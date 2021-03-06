<?php
	// Only return mark is scoring is enabled
	if (!empty($_SESSION[$_POST['lis_result_sourcedid']]['lis_outcome_service_url'])) {
		// Give participation mark
		$student_grade = 1;

		$grade_message = 'Thanks for responding. You have been given participation mark.';

		$outcome_url = $_SESSION[$_POST['lis_result_sourcedid']]['lis_outcome_service_url'];
		$consumer_key = $_SESSION[$_POST['lis_result_sourcedid']]['oauth_consumer_key'];
		$consumer_secret = '123456';

		$hmac_method = new OAuthSignatureMethod_HMAC_SHA1();

		$body = '<?xml version = "1.0" encoding = "UTF-8"?>
		<imsx_POXEnvelopeRequest xmlns = "http://www.imsglobal.org/services/ltiv1p1/xsd/imsoms_v1p0">
			<imsx_POXHeader>
				<imsx_POXRequestHeaderInfo>
					<imsx_version>V1.0</imsx_version>
					<imsx_messageIdentifier>' . $_SERVER['REQUEST_TIME'] . '</imsx_messageIdentifier>
				</imsx_POXRequestHeaderInfo>
			</imsx_POXHeader>
			<imsx_POXBody>
				<replaceResultRequest>
					<resultRecord>
						<sourcedGUID>
							<sourcedId>' . $_POST['lis_result_sourcedid'] . '</sourcedId>
						</sourcedGUID>
						<result>
							<resultScore>
								<language>en</language>
								<textString>' . $student_grade . '</textString>
							</resultScore>
						</result>
					</resultRecord>
				</replaceResultRequest>
			</imsx_POXBody>
		</imsx_POXEnvelopeRequest>';

		$hash = base64_encode(sha1($body, TRUE));
			$params = array('oauth_body_hash' => $hash);
		$token = '';
		$content_type = 'application/xml';

		$hmac_method = new OAuthSignatureMethod_HMAC_SHA1();
		$consumer = new OAuthConsumer($consumer_key, $consumer_secret);
		$outcome_request = OAuthRequest::from_consumer_and_token($consumer, $token, 'POST', $outcome_url, $params);
		$outcome_request->sign_request($hmac_method, $consumer, $token);

		$header = $outcome_request->to_header();
		$header = $header . "\r\nContent-type: " . $content_type . "\r\n";
		$options = array(
			'http' => array(
				'method' => 'POST',
				'content' => $body,
				'header' => $header,
			),
		);

		$ctx = stream_context_create($options);
		$fp = @fopen($outcome_url, 'rb', FALSE, $ctx);
		$response = @stream_get_contents($fp);
	}
?>

<html>
	<head>
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css"/>
		<link rel="stylesheet" href="css/response-map.css"/>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="//maps.googleapis.com/maps/api/js?v=3&key=AIzaSyCyyijfRSuYHQDxtZREVk20YImRrwwu_Is"></script>
		<script src="js/markerclusterer.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/d3/3.4.11/d3.min.js"></script>
		<script src="js/d3.layout.cloud.js"></script>
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

		<script type="text/javascript">
			var allStudentResponses = '<?php echo $all_student_responses ?>';
			var mapResponses = JSON.parse(allStudentResponses);
			var startLocation = new google.maps.LatLng(mapResponses[0].lat, mapResponses[0].lng);
			var sourcedid = '<?php echo $_POST["lis_result_sourcedid"] ?>';
			var sessid = '<?php echo $session_id ?>';

			var markerBounds = new google.maps.LatLngBounds();
			var iterator = 0;
			var map, markers = [];
			var openedMarker = null;

			function toggleThumbsUp(respid, element) {
				$.ajax({
					type: 'POST',
					url: 'vote.php',
					data: JSON.stringify({
						sourcedid: sourcedid,
						sessid: sessid,
						respid: respid,
					}),
					dataType: 'json',
					success: function(data) {
						console.log(data);
						console.log(element);
						var voteCountElem = $($(element).parent().find('.vote-count')[0]);

						if (data.vote) {
							if (!$(element).hasClass('btn-primary')) {
								openedMarker.voteCount = openedMarker.voteCount + 1;
							}

							$(element).removeClass('btn-default');
							$(element).addClass('btn-primary');
							openedMarker.thumbsUp = true;
						}
						else {
							if (!$(element).hasClass('btn-default')) {
								openedMarker.voteCount = openedMarker.voteCount - 1;
							}

							$(element).removeClass('btn-primary');
							$(element).addClass('btn-default');
							openedMarker.thumbsUp = false;
						}

						voteCountElem.text(openedMarker.voteCount);
					},
				});
			}

			function mapInitialise() {
				var mapOptions = {
					center: startLocation,
					zoomControl: true,
					streetViewControl: false,
				};

				map = new google.maps.Map(
					document.getElementById('map-canvas'),
					mapOptions
				);

				for (var key in mapResponses) {
					// First marker contains student's own response
					if (key === 0) {
						// Convert first marker lat and long to float for use in distance calculations
						mapResponses[0].lat = parseFloat(mapResponses[0].lat);
						mapResponses[0].lng = parseFloat(mapResponses[0].lng);

						// First marker is the centre marker
						mapResponses[0].distanceToCentre = 0;

						mapResponses[0].myMarker = true;
					}
					else {
						// Randomly nudge markers slightly to avoid overlap
						var nudgeLat = Math.random() * 0.00005;
						nudgeLat *= Math.floor(Math.random()*2) == 1 ? 1 : -1;

						var nudgeLng = Math.random() * 0.00005;
						nudgeLng *= Math.floor(Math.random()*2) == 1 ? 1 : -1;

						mapResponses[key].lat = parseFloat(mapResponses[key].lat) + nudgeLat;
						mapResponses[key].lng = parseFloat(mapResponses[key].lng) + nudgeLng;

						// Calculate the distance to centre marker
						mapResponses[key].distanceToCentre = Math.sqrt(Math.pow(mapResponses[key].lat - mapResponses[0].lat, 2) + Math.pow(mapResponses[key].lng - mapResponses[0].lng, 2));

						mapResponses[key].myMarker = false;
					}

					var marker = new google.maps.Marker({
						position: new google.maps.LatLng(mapResponses[key].lat, mapResponses[key].lng),
						map: map,
						draggable: false
					});

					marker.distanceToCentre = mapResponses[key].distanceToCentre;
					marker.fullname = mapResponses[key].fullname;
					marker.responseBody = mapResponses[key].response;
					marker.myMarker = mapResponses[key].myMarker;
					marker.fullImageUrl = mapResponses[key].image_url;
					marker.thumbnailImageUrl = mapResponses[key].thumbnail_url;
					marker.fullname = mapResponses[key].fullname;
					marker.responseId = mapResponses[key].id;
					marker.thumbsUp = mapResponses[key].thumbs_up;
					marker.voteCount = parseInt(mapResponses[key].vote_count);

					var contentString = '<div id="content">' +
											'<h3 id="firstHeading" class="firstHeading">' + marker.fullname + '</h3>' +
											'<div id="bodyContent">' +
												'<p>' + marker.responseBody + '</p>';

					if ((marker.thumbnailImageUrl !== null) && (marker.fullImageUrl !== null)) {
						contentString += 		'<a href="#myModal" data-toggle="modal"><img src="' + marker.thumbnailImageUrl + '" alt=""/></a>';
					}

					contentString +=			'<button type="button" class="vote-btn btn btn-default btn-xs" onclick="toggleThumbsUp(' + marker.responseId + ', this)"><i class="fa fa-thumbs-o-up"></i></button>' +
											'</div>' +
										'</div>';

					marker.infoWindow = new google.maps.InfoWindow();

					marker.infoWindow.setContent(contentString);

					markers.push(marker);

					google.maps.event.addListener(marker, 'click', function() {
						if (openedMarker !== null) {
							openedMarker.infoWindow.close();
							openedMarker = null;
						}

						$('.response-full-image').attr('src', this.fullImageUrl);
						$('.response-fullname').text(this.fullname + '\'s Image Response');

						var buttonClass = 'btn-default';

						if (this.thumbsUp) {
							buttonClass = 'btn-primary';
						}
						else {
							buttonClass = 'btn-default';
						}

						var contentString = '<div id="content">' +
											'<h3 id="firstHeading" class="firstHeading">' + this.fullname + '</h3>' +
											'<div id="bodyContent">' +
												'<p>' + this.responseBody + '</p>';

						if ((this.thumbnailImageUrl !== null) && (this.fullImageUrl !== null)) {
							contentString += 		'<a href="#myModal" data-toggle="modal"><img src="' + this.thumbnailImageUrl + '" alt=""/></a>';
						}

						contentString +=			'<div class="vote-group"><button type="button" class="vote-btn btn ' + buttonClass + ' btn-xs" onclick="toggleThumbsUp(' + this.responseId + ', this)"><i class="fa fa-thumbs-o-up"></i></button>' +
													'<span class="vote-count">' + this.voteCount + '</span></div>' +
												'</div>' +
											'</div>';

						this.infoWindow.setContent(contentString);

						this.infoWindow.open(map,this);
						openedMarker = this;
					});
				}

				// Sort the markers based on distance to center point
				markers.sort(function(a, b) {
					return (a.distanceToCentre - b.distanceToCentre);
				});

				// Only fit the view to a maximum of 20 closest markers
				var numVisibleMarkers = markers.length >= 28 ? 28 : markers.length;

				for (var i = 0; i < numVisibleMarkers; i++) {
					markerBounds.extend(markers[i].getPosition());
				}

				map.fitBounds(markerBounds);

				var mcOptions = {
					gridSize: 50,
					maxZoom: 18
				};

				var markerCluster = new MarkerClusterer(map, markers, mcOptions);
			}

			google.maps.event.addDomListener(window, 'load', mapInitialise);

			// Plotting word cloud
			$(function() {
				var frequencyList = <?php echo $word_frequency ?>;
				console.log("AAA");
				console.log(frequencyList);
				console.log("BBB");

				var color_range = ['#ddd', '#ccc', '#bbb', '#aaa', '#999', '#888', '#777', '#666', '#555', '#444', '#333', '#222'];
				var use_color = false;
				<?php if(isset($_POST['custom_usecolor']) && $_POST['custom_usecolor'] == 'true') { ?>
				use_color = true;
				<?php } ?>
				if(use_color) {
					color_range = d3.scale.category20().range();
				}

				var color = d3.scale.linear()
					.domain([0, 1, 2, 3, 4, 5, 6, 10, 15, 20, 70])
					.range(color_range);

				d3.layout.cloud().size([750, 240])
					.words(frequencyList)
					.rotate(0)
					.fontSize(function(d) { return d.size; })
					.on('end', draw)
					.start();

				function draw(words) {
					d3.select('.response-word-cloud').append('svg')
						.attr('width', 772)
						.attr('height', 250)
						.attr('class', 'wordcloud')
						.append('g')
						// without the transform, words words would get cutoff to the left and top, they would
						// appear outside of the SVG area
						.attr('transform', 'translate(340,125)')
						.selectAll('text')
						.data(words)
						.enter().append('text')
							.style('font-size', function(d) { return d.size + 'px'; })
							.style('fill', function(d, i) { return color(i); })

							.attr("text-anchor", "middle")
							.attr('transform', function(d) {
								return 'translate(' + [d.x, d.y] + ')rotate(' + d.rotate + ')';
							})
							.text(function(d) { return d.text; });
				}
			});
		</script>
	</head>

	<div id="map-canvas"></div>
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span>
						<span class="sr-only">Close</span>
					</button>
					<h4 class="modal-title response-fullname"></h4>
				</div>
				<div class="modal-body">
					<img class="response-full-image" src="">
				</div>
			</div>
		</div>
	</div>
	<form method="POST" enctype="multipart/form-data" action="update_location.php">
		<input type='hidden' name='postvars' value='<?php echo json_encode($_POST); ?>' />
		<input type="hidden" name="user_id" value="<?php echo $map_user_id; ?>" />
		<div class="input-group" style="margin-top:20px;">
			<input type="text" class="form-control user-location" name="user_location" value="<?php echo str_replace('"',"'",$map_location); ?>">
			<span class="input-group-btn">
				<input type="submit" class="btn btn-primary" value="Update Your Location" />
			</span>
		</div>
	</form>

	<?php if(isset($_POST['custom_showcloud']) && $_POST['custom_showcloud'] == 'true') { ?>
		<div class="response-word-cloud"></div>
	<?php } else {
	?>
		<div class="alert alert-success" role="alert">Thank you for posting.</div>
	<?php
	} ?>
</html>
