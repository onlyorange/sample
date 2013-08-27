<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<meta name="description" content="">
    	<meta name="author" content="">

    	<link href="{{ assets }}css/bootstrap.min.css" rel="stylesheet">
    	<link href="{{ assets }}css/style.css" rel="stylesheet">
		<script type="text/javascript" src="{{ assets }}js/bootstrap.min.js"></script>

		{% block head %}
			<title>{% block title %}My Title{% endblock %}</title>
		{% endblock %}

	</head>
	<body>
		<div class="container">

			<div class="masthead">
		        <ul class="nav nav-pills pull-right">
		         	<li class="active"><a href="{{root}}home/index">Home</a></li>
		         	<li><a href="{{root}}home/about">About</a></li>
		         	<li><a href="{{root}}home/contact">Contact</a></li>
		        </ul>
	        	<h3 class="muted">Project name</h3>
	      	</div>

	      	<hr/>

			<div class="hero-unit">
        		<h1>JL MVVM!</h1>
        		<p class="lead">A Model-View-ViewModel framework for php based on my best-practices</p>
        		<a class="btn btn-large btn-success" href="#">A button!</a>
      		</div>

			<div class="content">
				{% block content %}{% endblock %}
			</div>
			
			<footer>
        		{% block footer %}
	                &copy; Copyright 2013.
	            {% endblock %}
      		</footer>
		</div>
	</body>
</html>