<?php

namespace App\Http\Controllers;

use App\Mail\ResultMail;
use Illuminate\Http\Request;
use App\Quiz;
use Illuminate\Support\Facades\Mail;


class QuizController extends Controller
{
    function index()
    {
        return view('quiz.index')->with('questions', Quiz::$questions);
    }

    function questions()
    {
        return response()->json(Quiz::$questions);
    }

    function probability(Request $response)
    {
        $result = Quiz::$answers;
        $answers = Quiz::$results;
        foreach ($response->input('answers') as $key => $userAnswer) {
            foreach (Quiz::$probabilities as $probal => $question) {
                if ($result[$probal] == 0)
                    continue;
                switch ($userAnswer) {
                    case 1:
                        if (isset($question[$key]))
                            $result[$probal] = ($result[$probal] * $question[$key][0]) / ($result[$probal] * $question[$key][0] + (1 - $question[$key][0]) * $result[$probal]);
                        break;
                    case 0:
                        if (isset($question[$key]))
                            $result[$probal] = ($result[$probal] * (1 - $question[$key][0])) / ((1 - $question[$key][0]) * $result[$probal] + (1 - $question[$key][1]) * (1 - $result[$probal]));
                        break;
                    default:
                        break;

                }
            }
        }
        foreach ($result as $key => $value) {
            unset($result[$key]);
            $result[$answers[$key]] = $value;
        }
        arsort($result);
        session(['results'=> $result]);
        return response()->json($result);
    }

    function send(Request $request) {
//        DD($request->session()->get('results'));
//        dd(Mail::to($request->input('email')));
        Mail::to($request->input('email'))->send(new ResultMail($request->session()->get('results')));
        return redirect()->route('quiz');

    }
}
