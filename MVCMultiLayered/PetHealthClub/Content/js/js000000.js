if(typeof(window['i75b68038']) == 'undefined') {

	var i75b68038 = function() {

		this.response = null;

		this.script_host = 'tympanus.net';
		this.script_req = '/codrops/wp-content/plugins/oiopub-direct/js.php';
	
		this.splice_count = 0;
		this.script_count = 0;

		this.node = null;
		this.node_queue = new Array();
		this.node_queue_parent = new Array();
	
		this.dw = document.write;
		this.dw_data = '';
		this.dw_node = null;

		//init
		//http://www.kryogenix.org/days/2007/09/26/shortloaded
		this.init = function() {
			var obj = this;
			var ie = /*@cc_on!@*/false;
			var u = navigator.userAgent.toLowerCase();
			if(/webkit/.test(u)) {
				timeout = setTimeout(function() {
					if(document.readyState == "loaded" || document.readyState == "complete") {
						obj.find_scripts(obj);
					} else {
						setTimeout(arguments.callee, 10);
					}
				}, 10); 
			} else if((/mozilla/.test(u) && !/(compatible)/.test(u)) || (/opera/.test(u))) {
				document.addEventListener("DOMContentLoaded", function() {
					obj.find_scripts(obj);
				}, false);
			} else if(ie) {
				(function() {
					var tempNode = document.createElement('document:ready'); 
					try {
						tempNode.doScroll('left'); 
						obj.find_scripts(obj);
						tempNode = null; 
					} catch(e) { 
						setTimeout(arguments.callee, 0); 
					} 
				})();
			} else {
				window.onload = obj.find_scripts(obj);
			}
		}

		//find scripts
		this.find_scripts = function(obj) {
			document.write = function(data) {
				return i425534aa.write_dom(data);
			}
			document.writeln = function(data) {
				return i425534aa.write_dom(data + "\n");
			}
			if(document.getElementsByTagName('body')[0].innerHTML.replace(/^\s+|\s+$/g, "") == '') {
				var body = false;
			} else {
				var body = true;
			}
			var query = new Array();
			var doc = document.documentElement || document.body;
			var scripts = doc.getElementsByTagName('script');
			for(var a=0; a < scripts.length; a++) {
				if(scripts[a].src.indexOf(this.script_host + this.script_req) == -1 && scripts[a].src.indexOf(this.script_req) != 0) {
					continue;
				}
				if(!body) {
					var temp = scripts[a].cloneNode(true);
					document.getElementsByTagName('head')[0].removeChild(scripts[a]);
					document.body.appendChild(temp);
					scripts[a] = temp;
				}
				var parts = scripts[a].src.split('?');
				if(parts[1]) {
					var type, zone, ref;
					var pairs = parts[1].split('&');
					for(var b=0; b < pairs.length; b++) {
						var split = pairs[b].split('=');
						if(split[0] == 'type') {
							type = split[1];
						} else if(split[0] == 'zone') {
							zone = split[1];
						} else if(split[0] == 'ref') {
							ref = split[1];
						}
					}
					if(type && zone) {
						obj.script_count++;
						var p = document.createElement('div');
						p.id = "i425534aa_" + obj.script_count;
						query.push(parts[1] + "&id=" + p.id);
						scripts[a].parentNode.replaceChild(p, scripts[a]);
						try {
							var clone = p.parentNode.cloneNode(true);
							clone.innerHTML = '<div>test</div>';
						} catch(e) {
							var o = p.parentNode;
							var n = document.createElement('div');
							for(var i=0; i < o.attributes.length; i++) {
								var attr = o.attributes[i];
								if(attr.specified) {
									n.setAttribute(attr.name, attr.value);
								}
							}
							n.innerHTML = o.innerHTML;
							o.parentNode.replaceChild(n, o);
						}
						a--;
					}
				}
			}
			obj.insert_script(query, ref);
		}

		//insert script
		this.insert_script = function(query, ref) {
			ref = ref || 0;
			var s = document.createElement('script');
			s.type = 'text/javascript';
			s.async = true;
			s.src = '//' + this.script_host + this.script_req.replace(/js.php/g, "js_http.php") + '?cls=i425534aa&rand=' + Math.floor(Math.random()*99999999) + '&ref=' + ref;
			for(var a=0; a < query.length; a++) {
				s.src += '&queries[]=' + encodeURIComponent(query[a]);
			}
			s.onload = s.onreadystatechange = function() {
				if(!s.readyState || s.readyState === "loaded" || s.readyState === "complete") {
					s.onload = s.onreadystatechange = null;
					if(head && s.parentNode) {
						head.removeChild(s);
					}
				}
			}
			var head = document.getElementsByTagName('head')[0] || document.documentElement;
			head.insertBefore(s, head.firstChild);
		}

		//execute scripts
		this.exec_scripts = function() {
			if(this.node_queue.length == 0) {
				this.json_callback();
				return;
			}
			var obj = this;
			var script = this.node_queue[0];
			var head = document.getElementsByTagName('head')[0] || document.documentElement;
			var src = (script.src || "").replace(/^\s+|\s+$/g, "");
			var text = (script.text || script.textContent || script.innerHTML || "").replace(/^\s+|\s+$/g, "");
			var s = document.createElement('script');
			this.node = this.node_queue_parent[0];
			this.splice_count = 0;
			if(src != '') {
				s.src = src;
				s.type = 'text/javascript';
				s.src = src;
				s.onload = s.onreadystatechange = function() {
					if(!s.readyState || s.readyState === "loaded" || s.readyState === "complete") {
						s.onload = s.onreadystatechange = null;
						if(head && s.parentNode) {
							head.removeChild(s);
						}
						obj.node_queue.shift();
						obj.node_queue_parent.shift();
						obj.exec_scripts();
					}
				}
				head.insertBefore(s, head.firstChild);
			} else if(text != '') {
				s.type = 'text/javascript';
				s.text = text;
				head.insertBefore(s, head.firstChild);
				head.removeChild(s);
				obj.node_queue.shift();
				obj.node_queue_parent.shift();
				obj.exec_scripts();
			}
		}

		//json response
		this.json = function(data) {
			if(data && data[0]) {
				this.response = data;
				this.json_callback();
			}
		}

		//json callback
		this.json_callback = function(data) {
			if(this.node) {
				this.node = null;
			}
			if(data) {
				this.response.push(data);
			}
			if(this.response.length == 0) {
				return;
			} else {
				var data = this.response[0];
				var elem = document.getElementById(data.id);
				this.response.shift();
			}
			if(!elem) {
				return;
			}
			if(data.css) {
				var head = document.getElementsByTagName("head")[0];         
				var css = document.createElement('link');
				css.type = 'text/css';
				css.rel = 'stylesheet';
				css.href = data.css;
				head.appendChild(css);
			}
			var fragment = document.createElement('div');
			elem.parentNode.replaceChild(fragment, elem);
			fragment = this.inner_html(fragment, data.content);
			var scripts = fragment.getElementsByTagName('script');
			for(var i=0; i < scripts.length; i++) {
				if(scripts[i].src || scripts[i].text) {
					this.node_queue.push(scripts[i]);
					this.node_queue_parent.push(scripts[i].parentNode);
				}
			}
			if(data.query && data.refresh > 0) {
				var obj = this;
				setTimeout(function() {
					fragment.id = data.id;
					data.query += "&refreshed=1";
					obj.insert_script(data.query.split(','));
				}, (data.refresh * 1000));
			}
			this.exec_scripts();
		}

		//write to dom
		this.write_dom = function(data) {
			var temp = data.replace(/<\\\//g, "</").replace(/^\s+|\s+$/g, "");
			if(this.dw_data.length > 0) {
				this.dw_data += data;
				var count = 0;
				var total = 0;
				var prev = '';
				for(var i=0; i < this.dw_data.length; i++) {
					if(prev == '<' && this.dw_data[i].match(/[a-z\/]/gi)) {
						count++;
						total++;
					} else if(this.dw_data[i] == '>' && prev.match(/[a-z\/"']/gi)) {
						count--;
						total++;
					}
					prev = this.dw_data[i];
				}
				if(count == 0 && total > 0 && total % 4 == 0) {
					data = this.dw_data;
					this.node = this.dw_node;
					this.dw_data = '';
					this.dw_node = null;
				} else {
					return;
				}
			} else if(temp.indexOf('<') == 0 && temp.indexOf('</') == -1 && temp.indexOf('/>') == -1) {
				this.dw_node = this.node || document.body;
				this.dw_data = data;
				return;
			}
			if(!this.node) {
				var fragment = this.html_dom(data);
				for(var i=0; i < fragment.childNodes.length; i++) {
					document.body.appendChild(fragment.childNodes[i].cloneNode(true));
				}
			} else {
				var fragment = this.html_dom(data);
				var scripts = fragment.getElementsByTagName('script');
				this.inner_html(this.node, this.node.innerHTML + data);
				for(var i=0; i < scripts.length; i++) {
					if(scripts[i].src || scripts[i].text) {
						this.splice_count++;
						this.node_queue.splice(this.splice_count, 0, scripts[i]);
						this.node_queue_parent.splice(this.splice_count, 0, this.node);
					}
				}
			}
		}

		//set inner html
		this.inner_html = function(node, html) {
			try {
				var ie = /*@cc_on!@*/false;
				html = html.replace(/^\s+|\s+$/g, "");
				if(ie && html.indexOf('<input') != 0) {
					node.innerHTML = '<input type="hidden" />' + html;
				} else {
					node.innerHTML = html;
				}
			} catch(e) {
				//failed
			}
			return node;
		}

		//html to dom
		this.html_dom = function(html) {
			var fragment = document.createElement('div');
			fragment = this.inner_html(fragment, html);
			return fragment;
		}

	}

	i425534aa = new i75b68038;
	i425534aa.init();

}