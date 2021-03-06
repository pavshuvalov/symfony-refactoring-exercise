# Список API методов

## `GET /todo`

Возвращает список невыполненных задач.

Параметры запроса:

| Поле | Тип | Значение по-умолчанию | Описание |
|------|-----|-----------------------|----------|
| `all` | `1` | | Вернуть список всех задач |
| `offset` | `int` | `0` | Вернуть список задач со смещением |
| `length` | `int`, `1..200` | `20` | Вернуть определённое число задач |

Поле ответа `has_next` показывает, остались ли следующие задачи, которые запрос не вернул.

Пример ответа:

```json
{
	"status": "ok",
	"response": {
		"todos": {
			"id": 4,
			"text": "Share the result",
			"completed": 0
		},
		"has_next": false
	}
}
```

## `PATCH /todo/{id}`

Изменить задачу. Изменённая задача передаётся в теле запроса JSON-объектом

Пример тела запроса:

```json
{
	"completed": true
}
```

Пример ответа:

```json
{
	"status": "ok",
	"response": {}
}
```
