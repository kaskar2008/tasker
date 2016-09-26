function parseNodes(nodes) { // takes a nodes array and turns it into a <ul>
	if(nodes.length) {
		var ul = document.createElement("ul");
		ul.setAttribute('tasks', '');
		for(var i=0; i<nodes.length; i++) {
			ul.appendChild(parseNode(nodes[i]));
		}
		return ul;
	} else return null;
}

function parseNode(node) { // takes a node object and turns it into a <li>
	var li = taskElement(node);
	if(node.subtasks && node.subtasks.length) li.appendChild(parseNodes(node.subtasks));
	return li;
}

function taskElement(task) {
	var li = document.createElement("li");
	li.setAttribute('task', task.id);
	li.setAttribute('parent', task.parent);
	li.innerHTML = '<input z2 state="'+task.status+'" type="checkbox" '+(task.status==0 ? "checked" : "")+' onchange="onStateChange(this);"><div inner><span buttons bottom tac z2><span delete title="Delete this task">&#10005;</span><span add title="Add subtask">+</span></span><span _name>' + task.name + '</span></div>';
	// li.querySelector('input[type="checkbox"][state]').onchange = function() {
	// 	onStateChange(this);
	// }
	changeTaskState(li, task.status, true);
	return li;
}

function checkParentStatus(el) {
	var parent = el.closest('[task="'+el.getAttribute("parent")+'"]');
	if(parent) {
		var subtasks = parent.querySelectorAll('[task]');
		var checkedSubtasks = 0;
		for(var i=0; i<subtasks.length; i++) {
			var checkbox = subtasks[i].querySelector('input[type="checkbox"][state]');
			if(checkbox) {
				if(checkbox.checked) checkedSubtasks++;
			}
		}
		if(checkedSubtasks == subtasks.length) {
			changeTaskState(parent, 0);
		} else if(checkedSubtasks == 0) {
			changeTaskState(parent, 1);
		} else if(checkedSubtasks < subtasks.length) {
			changeTaskState(parent, 2);
		}
		checkParentStatus(parent);
	}
}

function checkChildrenStatus(el) {
	var _task = el.closest("[task]");
	changeTaskState(_task, el.checked ? 0 : 1);
	var tasks = _task.querySelectorAll('[tasks] [task]');
	for(var i=0; i<tasks.length; i++) {
		changeTaskState(tasks[i], el.checked ? 0 : 1);
	}
	checkParentStatus(_task);
}

function onStateChange(el) {
	checkChildrenStatus(el);
}

function changeTaskState(task, state, static) {
	var checkbox = task.querySelector('input[type="checkbox"][state]');
	var _tid = task.getAttribute('task');
	if(checkbox) {
		switch(state) {
			case 0: 
			checkbox.checked = true;
			checkbox.indeterminate = false;
			if(_filter_ == 'active') {
				task.setAttribute("remove", '');
			}
			break;

			case 1: 
			checkbox.checked = false;
			checkbox.indeterminate = false;
			if(_filter_ == 'done') {
				task.setAttribute("remove", '');
			}
			break;

			case 2: 
			checkbox.checked = false;
			checkbox.indeterminate = true;
			if(_filter_ == 'done') {
				task.setAttribute("remove", '');
			}
			break;
		}
	}
	if(!static) {
		saveState({tid: _tid, state: state}, function(result) {
			
		});
	} else {

	}
}

function saveState(params, callback) {
	var _id = params.tid || null;
	var _state = params.state!==null ? params.state : null;
	if(_id!==null && _state!==null) {
		api({method: 'put', controller: 'tasks/'+_id+'/state', data: {"state": _state}}, function(status, result) {
			callback(result);
		});
	}
}

function ajax(params, callback) {
	var _async = params.async!=null ? params.async : true;
	var _url = params.url || '';
	var _data = (params.data ? JSON.stringify(params.data) : null);
	var _method = params.method || 'GET';
	var _responseType = params.responseType || 'text';
	var error = null;

	if(_url != '') {
		var xhr = new XMLHttpRequest();
		if(_async) {
			xhr.onload = function() {
				if (xhr.readyState === 4) {
					var _response = getResponse(_responseType, xhr.responseText);
					if(!_response) error = xhr.statusText;
					if(callback) callback(error, xhr.status, _response);
				}
			};
		}
		xhr.open(_method, _url, _async);
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhr.send(_data);
		if(!_async) {
			var _response = getResponse(_responseType, xhr.responseText);
			if(!_response) error = xhr.statusText;
			return {
				status: xhr.status,
				result: _response || error
			};
		} else return true;
	}
}

function getResponse(rType, rData) {
	switch(rType) {
		case 'text': return rData; break;
		case 'json': 
			try {
				return JSON.parse(rData);
			} catch (e) {
				return null;
			}
			break;
		default: return rData;
	}
}

function api(params, callback) {
	var _controller = params.controller || null;
	if(_controller) {
		var _method = params.method || 'get';
		var _data = params.data || {};
		ajax({
			url: '/api/' + _controller,
			method: _method,
			responseType: 'json',
			async: true,
			data: _data
		}, function(error, status, result) {
			if(callback) callback(status, result);
		});
	}
}

function getTasks(filter, callback) {
	api({method: 'get', controller: 'tasks?state='+filter}, function(status, result) {
		callback(result);
	});
}

var _filter_ = 'active';
document.querySelector("[page]").setAttribute("filter", _filter_);

(function() {

	function getSubTasks(parent) {
		var tasks = parent.querySelectorAll("[tasks] [task]");
		var ids = [parent.getAttribute("task")];
		if(tasks && tasks.length) {
			for(var i=0; i<tasks.length; i++) {
				ids.push(tasks[i].getAttribute("task"));
			}
		}
		return ids;
	}

	function deleteTasks(parent) {
		var ids = getSubTasks(parent);
		api({method: 'delete', controller: 'tasks', data: ids}, function(status, result) {
			if(result.result) {
				parent.remove();
			} else {
				alert(result.error);
			}
		});
	}

	function createTaskElement(parent, data) {
		var parentEl;
		if(parent == 0) {
			parentEl = document.querySelector('[list]>[tasks]');
			if(!parentEl) {
				var ul = document.createElement("ul");
				ul.setAttribute('tasks', '');
				document.querySelector('[list]').appendChild(ul);
				parentEl = document.querySelector('[list]>[tasks]');
			}
		} else {
			parentEl = document.querySelector('[task="'+parent+'"] [tasks]');
		}
		var li = taskElement(data);
		parentEl.appendChild(li);
	}

	function showNewEdit(parent) {
		var subtasks = parent.querySelector("[tasks]");
		var li = document.createElement("li");
		var edit = document.createElement("input");
		edit.setAttribute('type', 'text');
		edit.setAttribute('placeholder', 'Enter new name for the task');
		if(!subtasks) {
			var ul = document.createElement("ul");
			ul.setAttribute('tasks', '');
			parent.appendChild(ul);
			subtasks = parent.querySelector("ul");
		}
		li.insertBefore(edit, li.firstChild);
		subtasks.insertBefore(li, subtasks.firstChild);
		edit.onkeypress = function(event) {
			if(event.keyCode === 13) {
				var that = this;
				event.preventDefault();
				that.setAttribute('disabled', 'true');
				var _name = that.value;
				newTask({ name: _name, parent: parent.getAttribute("task"), description: '' }, function(result) {
					if(result.result) createTaskElement(parent.getAttribute("task"), result.data);
					else alert(result.error);
				});
			}
		}
		edit.onblur = function(event) {
			event.preventDefault();
			this.parentElement.remove();
		}
		
		edit.focus();
	}

	function saveTask(task, callback) {
		var tid = task.closest("[task]").getAttribute("task") || null;
		var _data = {name: task.innerHTML||null };
		if(tid) {
			api({method: 'put', controller: 'tasks/'+tid, data: _data}, function(status, result) {
				callback(result);
			});
		}
	}

	function newTask(params, callback) {
		api({method: 'post', controller: 'tasks', data: params}, function(status, result) {
			callback(result);
		});
	}

	var page = document.querySelector("[page]");
	var list = page.querySelector("[list]");
	list.addEventListener("DOMSubtreeModified", function(e) {
		var tasks = list.querySelectorAll("[inner] [_name]");
		for(var i=0; i<tasks.length; i++) {
			var task = tasks[i].closest("[task]");
			var addbtn = task.querySelector("[add]");
			var delbtn = task.querySelector("[delete]");
			addbtn.onclick = function(event) {
				event.preventDefault();
				showNewEdit(this.closest("[task]"));
			}
			delbtn.onclick = function(event) {
				event.preventDefault();
				deleteTasks(this.closest("[task]"));
			}
			tasks[i].onclick = function(event) {
				this.setAttribute('contenteditable', 'true');
				this.focus();
				event.preventDefault();
			}
			tasks[i].onkeypress = function(event) {
				if(event.keyCode === 13) {
					this.setAttribute('contenteditable', 'false');
					event.preventDefault();
					saveTask(this, function(result) {
						
					});
				}
			}
			tasks[i].onblur = function(event) {
				this.setAttribute('contenteditable', 'false');
			}
		}
	}, false);
	var edit = document.querySelector("[root-task]");
	edit.onkeypress = function(event) {
		if(event.keyCode === 13) {
			event.preventDefault();
			var _name = this.value;
			var that = this;
			newTask({ name: _name, parent: 0, description: '' }, function(result) {
				if(result.result) {
					createTaskElement(0, result.data);
					that.value = '';
				}
				else {
					alert(result.error);
				}
			});
		}
	}
	var switchers = document.querySelectorAll("a[switch]");
	for(var i=0; i<switchers.length; i++) {
		switchers[i].onclick = function(event) {
			event.preventDefault();
			_filter_ = this.getAttribute("switch");
			document.querySelector("[page]").setAttribute("filter", _filter_);
			getTasks(_filter_, function(result) {
				if(result.status) {
					var tasksElems = parseNodes(result.data);
					list.innerHTML = '';
					list.appendChild(tasksElems);
				} else {
					list.innerHTML = '';
				}
			});
		}
	}
	getTasks(_filter_, function(result) {
		if(result.status) {
			var tasksElems = parseNodes(result.data);
			list.innerHTML = '';
			list.appendChild(tasksElems);
		} else {
			list.innerHTML = '';
		}
	});

})();