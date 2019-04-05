function urlencode(data) {
	const output = [];
	for (const [k, v] of Object.entries(data)) {
		output.push(encodeURIComponent(k) + '=' + encodeURIComponent(v));
	}
	return output.join('&');
}

function createCompletedTodo(id, text) {
	const li = document.createElement('li');
	li.style.textDecoration = 'line-through';
	li.id = 'todo_item_' + id;
	li.textContent = text;
	const button = document.createElement('button');
	button.onclick = () => reset(id, text);
	button.textContent = 'reset';
	button.dataset.id = id;
	button.dataset.text = text;

	// add button inside li
	li.textContent += ' ';
	li.appendChild(button);

	return li;
}

function createUncompletedTodo(id, text) {
	const li = document.createElement('li');
	li.id = 'todo_item_' + id;
	li.textContent = text;
	const button = document.createElement('button');
	button.onclick = () => complete(id, text);
	button.textContent = 'complete';
	button.dataset.id = id;
	button.dataset.text = text;

	// add button inside li
	li.textContent += ' ';
	li.appendChild(button)

	return li;
}

function complete(id, text) {
	const request_body = JSON.stringify({
		completed: true,
	});
	fetch('/api/todo/' + id, {
		method: 'PATCH',
		headers: {
			'content-type': 'application/json',
		},
		body: request_body,
	})
		.then(response => {
			const el = document.getElementById('todo_item_' + id);
			el.parentNode.replaceChild(createCompletedTodo(id, text), el);
		});
}

function reset(id, text) {
	const request_body = JSON.stringify({
		completed: false,
	});
	fetch('/api/todo/' + encodeURIComponent(id), {
		method: 'PATCH',
		headers: {
			'content-type': 'application/json',
		},
		body: request_body,
	})
		.then(response => {
			const el = document.getElementById('todo_item_' + id);
			el.parentNode.replaceChild(createUncompletedTodo(id, text), el);
		});
}

function showTodos(all, offset, length) {
	const query_parameters = urlencode({
		all: all ? 1 : 0,
		offset: offset,
		length: length,
	});
	fetch('/api/todo?' + query_parameters)
		.then(response => response.json())
		.then(response => response['response'])
		.then(response => {
			const el = document.getElementById('todo-list');

			// remove all child nodes
			while (el.firstChild) {
				el.removeChild(el.firstChild);
			}

			for (const v of response['todos']) {
				let node;
				if (v['completed']) {
					node = createCompletedTodo(v['id'], v['text']);
				} else {
					node = createUncompletedTodo(v['id'], v['text']);
				}
				el.appendChild(node);
			}
		});
}

// show first 100 todos
showTodos(false, 0, 100);
