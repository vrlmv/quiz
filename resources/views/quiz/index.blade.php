<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}"></script>

    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .content {
            text-align: center;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }
        #loader {
            display: none;
            position: fixed;
            z-index: 999999;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: rgba( 255, 255, 255, .8 ) url({{asset('loader.svg')}}) 50% 50% no-repeat;
        }
        body.loading #loader {
            overflow: hidden;
        }

        body.loading #loader {
            display: block;
        }
    </style>
</head>
<body>
<div class="progress">
    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
         aria-valuemax="100"></div>
</div>
<div class="flex-center position-ref full-height">
    <div class="content">
        <div class="container text-left">
            <div>
                <h1 id="question" data-itter="0">Question</h1>
            </div>
            <div class="m-auto">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="answer" id="answer1" value="1" checked>
                    <label class="form-check-label" for="answer1">
                        Да
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="answer" id="answer2" value="0">
                    <label class="form-check-label" for="answer2">
                        Нет
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="answer" id="answer3" value="0.5">
                    <label class="form-check-label" for="answer3">
                        Не знаю
                    </label>
                </div>
                <div class="btn-group">
                    <button class="btn btn-secondary text-light down" disabled>Предыдущий вопрос</button>
                    <button class="btn btn-secondary text-light up" >Следующий вопрос </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="results" aria-hidden="true" id="resultModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resultsLabel">Результаты теста:</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('send') }}">
                @csrf

                <div class="modal-body">
                <div class="results">

                </div>
                <br><p>
                    Отправить результаты на почту?
                </p>
                <div class="form-group">
                    <label for="email">Адрес почты</label>
                    <input type="email" class="form-control" name="email" id="email" aria-describedby="emailHelp" placeholder="Введите почту">
                    <small id="emailHelp" class="form-text text-muted">Мы ни кому не дадим вашу почту. Если вы не видите письма на почте проверьте пожалуйста папку "спам"</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-btn" data-dismiss="modal">Закрыть</button>
                <button type="submit" class="btn btn-primary send-btn">Отправить</button>
            </div>
            </form>
        </div>
    </div>
</div>
<div id="loader" class="modal"></div>
</body>

<script>
    var questions,
        answers = [],
        key = 0;
    $.ajax({
        url: '{{ route('questions') }}',
        method: 'GET',
        success: function (data) {
            questions = Object.values(data);
            getAnswers(data);
        },
        error: function (data) {
            console.log(data);
        }
    });

    function appendResults(data) {
        let html = "<div class=\"progress\">\n" +
            "    <div class=\"progress-bar text-dark\" role=\"progressbar\" style=\"width: 0%;\" aria-valuenow=\"0\" aria-valuemin=\"0\"\n" +
            "         aria-valuemax=\"100\"></div>" +
            "</div>",
            results = $('.results');
        Object.keys(data).forEach(function (key) {
            console.log(key + " - " + data[key]);
            results.append(key + "</br>" + html);
            let progress = $('.progress-bar').last(),
                num = data[key]*100,
                perCent = parseInt(num.toFixed(2)) +  '%';
            progress.css('width', num + '%');
            progress.attr('aria-valuenow', num.toFixed(2)).append(perCent);
        });
        $('#resultModal').modal('show');
        $('.up').attr('disabled', true);
        $('.down').attr('disabled', true);
    }

    function getAnswers() {
        let question = $('#question');
        question.attr('data-itter', key);
        question.html(questions[key]);
    }



    $(document).ready(function () {
        $body = $("body");
        $(document).on({
            ajaxStart: function() { $body.addClass("loading");    },
            ajaxStop: function() { $body.removeClass("loading"); }
        });
        let question = $('#question');
        $('.down').on('click', function (e) {
            let radioValue = $("input[name='answer']:checked").val();
            e.preventDefault();
            answers[key] = radioValue;
            if (key > 0) {
                (this).removeAttribute('disabled');
                key--;
                if (key === 0) {
                    $('.down').attr('disabled', true);
                }
                question.html(questions[key]);
            } else {
                (this).attr('disabled', true);
            }
            console.log(answers);
        });

        $('.cancel-btn').on('click', function (e) {
            e.preventDefault();
            location.reload();
        })

        $('.up').on('click', function (e) {
            let radioValue = $("input[name='answer']:checked").val();
            $('.down').prop('disabled', false);
            e.preventDefault();
            answers[key] = radioValue;
            if (key === questions.length - 2) {
                $(this).text('Отправить результаты');
            }
            if (key < questions.length - 1) {
                key++;
                console.log(key);

                question.html(questions[key]);
            } else {
                $.ajax({
                    beforeSend: function() {
                        $('#loader').show();
                    },
                    complete: function() {
                        $('#loader').hide();
                    },
                    url: '{{ route('probability') }}',
                    method: 'POST',
                    data: {'answers': answers, "_token": "{{ csrf_token() }}",},
                    success: function (data) {

                        appendResults(data);
                    },
                    error: function (data) {
                        console.log(data);
                    }
                })
            }

        });
    })
</script>
</html>
