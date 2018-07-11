@extends('layouts.menuBar')

@section('title')
    تعریف کارنامه ی آزمون
@stop

@section('reminder')

    <center style="margin-top: 130px">
        <div>
            <h3>تعریف وضعیت آزمون</h3>
            <div class="line"></div>
        </div>
        <div class="myRegister">
            <div class="data row">
                <form method="post" action="{{URL('defineKarname')}}">
                    @if(!isset($quizId))
                        <div class="col-xs-12">
                            <label>
                                <span>آزمون مورد نظر</span>
                                @if(count($quizes) == 0)
                                    <p class="warning_color" style="margin-top: 10px">آزمونی جهت نمایش وجود ندارد</p>
                                @else
                                    <select name="quizId">
                                        @foreach($quizes as $quiz)
                                            <option value="{{$quiz->id}}">{{$quiz->QN}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <input type="submit" class="MyBtn" style="width: auto" name="submitKindKarname" value="تایید">
                        </div>
                    @else
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش ضرایب دروس</span>
                                @if($kindKarname->coherences)
                                    <input type="checkbox" name="coherences" checked>
                                @else
                                    <input type="checkbox" name="coherences">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش نمودار مقایسه ای در کارنامه ی کلی</span>
                                @if($kindKarname->lessonBarChart)
                                    <input type="checkbox" name="lessonBarChart" checked>
                                @else
                                    <input type="checkbox" name="lessonBarChart">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش نمودار مقایسه ای در کارنامه ی مبحثی</span>
                                @if($kindKarname->subjectBarChart)
                                    <input type="checkbox" name="subjectBarChart" checked>
                                @else
                                    <input type="checkbox" name="subjectBarChart">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش نمودار مقایسه ای در کارنامه ی حیطه ای</span>
                                @if($kindKarname->compassBarChart)
                                    <input type="checkbox" name="compassBarChart" checked>
                                @else
                                    <input type="checkbox" name="compassBarChart">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span> نمایش میانگین درصد پاسخ گویی در کارنامه ی کلی</span>
                                @if($kindKarname->lessonAvg)
                                    <input type="checkbox" name="lessonAvg" checked>
                                @else
                                    <input type="checkbox" name="lessonAvg">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span> نمایش میانگین درصد پاسخ گویی در کارنامه ی مبحثی</span>
                                @if($kindKarname->subjectAvg)
                                    <input type="checkbox" name="subjectAvg" checked>
                                @else
                                    <input type="checkbox" name="subjectAvg">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span> نمایش میانگین درصد پاسخ گویی در کارنامه ی حیطه ای</span>
                                @if($kindKarname->compassAvg)
                                    <input type="checkbox" name="compassAvg" checked>
                                @else
                                    <input type="checkbox" name="compassAvg">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش وضعیت در کارنامه ی کلی</span>
                                @if($kindKarname->lessonStatus)
                                    <input type="checkbox" name="lessonStatus" checked>
                                @else
                                    <input type="checkbox" name="lessonStatus">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش وضعیت در کارنامه ی مبحثی</span>
                                @if($kindKarname->subjectStatus)
                                    <input type="checkbox" name="subjectStatus" checked>
                                @else
                                    <input type="checkbox" name="subjectStatus">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش وضعیت در کارنامه ی حیطه ای</span>
                                @if($kindKarname->compassStatus)
                                    <input type="checkbox" name="compassStatus" checked>
                                @else
                                    <input type="checkbox" name="compassStatus">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش بیشترین درصد در هر درس</span>
                                @if($kindKarname->lessonMaxPercent)
                                    <input type="checkbox" name="lessonMaxPercent" checked>
                                @else
                                    <input type="checkbox" name="lessonMaxPercent">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش بیشترین درصد در هر مبحث</span>
                                @if($kindKarname->subjectMaxPercent)
                                    <input type="checkbox" name="subjectMaxPercent" checked>
                                @else
                                    <input type="checkbox" name="subjectMaxPercent">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش بیشترین درصد در هر حیطه</span>
                                @if($kindKarname->compassMaxPercent)
                                    <input type="checkbox" name="compassMaxPercent" checked>
                                @else
                                    <input type="checkbox" name="compassMaxPercent">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش رتبه در شهر در کارنامه ی کلی</span>
                                @if($kindKarname->lessonCityRank)
                                    <input type="checkbox" name="lessonCityRank" checked>
                                @else
                                    <input type="checkbox" name="lessonCityRank">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش رتبه در شهر در کارنامه ی مبحثی</span>
                                @if($kindKarname->subjectCityRank)
                                    <input type="checkbox" name="subjectCityRank" checked>
                                @else
                                    <input type="checkbox" name="subjectCityRank">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش رتبه در شهر در کارنامه ی حیطه ای</span>
                                @if($kindKarname->compassCityRank)
                                    <input type="checkbox" name="compassCityRank" checked>
                                @else
                                    <input type="checkbox" name="compassCityRank">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش رتبه در استان در کارنامه ی کلی</span>
                                @if($kindKarname->lessonStateRank)
                                    <input type="checkbox" name="lessonStateRank" checked>
                                @else
                                    <input type="checkbox" name="lessonStateRank">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش رتبه در استان در کارنامه ی مبحثی</span>
                                @if($kindKarname->subjectStateRank)
                                    <input type="checkbox" name="subjectStateRank" checked>
                                @else
                                    <input type="checkbox" name="subjectStateRank">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش رتبه در استان در کارنامه ی حیطه ای</span>
                                @if($kindKarname->compassStateRank)
                                    <input type="checkbox" name="compassStateRank" checked>
                                @else
                                    <input type="checkbox" name="compassStateRank">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش رتبه در کشور در کارنامه ی کلی</span>
                                @if($kindKarname->lessonCountryRank)
                                    <input type="checkbox" name="lessonCountryRank" checked>
                                @else
                                    <input type="checkbox" name="lessonCountryRank">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش رتبه در کشور در کارنامه ی مبحثی</span>
                                @if($kindKarname->subjectCountryRank)
                                    <input type="checkbox" name="subjectCountryRank" checked>
                                @else
                                    <input type="checkbox" name="subjectCountryRank">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش رتبه در کشور در کارنامه ی حیطه ای</span>
                                @if($kindKarname->compassCountryRank)
                                    <input type="checkbox" name="compassCountryRank" checked>
                                @else
                                    <input type="checkbox" name="compassCountryRank">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش تراز کلی</span>
                                @if($kindKarname->generalTaraz)
                                    <input type="checkbox" name="generalTaraz" checked>
                                @else
                                    <input type="checkbox" name="generalTaraz">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش رتبه کلی در شهر</span>
                                @if($kindKarname->generalCityRank)
                                    <input type="checkbox" name="generalCityRank" checked>
                                @else
                                    <input type="checkbox" name="generalCityRank">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش رتبه کلی در استان</span>
                                @if($kindKarname->generalStateRank)
                                    <input type="checkbox" name="generalStateRank" checked>
                                @else
                                    <input type="checkbox" name="generalStateRank">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش رتبه کلی در کشور</span>
                                @if($kindKarname->generalCountryRank)
                                    <input type="checkbox" name="generalCountryRank" checked>
                                @else
                                    <input type="checkbox" name="generalCountryRank">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش تراز در هر درس</span>
                                @if($kindKarname->partialTaraz)
                                    <input type="checkbox" name="partialTaraz" checked>
                                @else
                                    <input type="checkbox" name="partialTaraz">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش نمره از در کارنامه ی کلی</span>
                                @if($kindKarname->lessonMark)
                                    <input type="checkbox" name="lessonMark" checked>
                                @else
                                    <input type="checkbox" name="lessonMark">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش نمره از در کارنامه ی مبحثی</span>
                                @if($kindKarname->subjectMark)
                                    <input type="checkbox" name="subjectMark" checked>
                                @else
                                    <input type="checkbox" name="subjectMark">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <span>نمایش نمره از در کارنامه ی حیطه ای</span>
                                @if($kindKarname->compassMark)
                                    <input type="checkbox" name="compassMark" checked>
                                @else
                                    <input type="checkbox" name="compassMark">
                                @endif
                            </label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="quizId" value="{{$quizId}}" hidden>
                            <input type="submit" class="MyBtn" style="width: auto" value="تایید" name="doDefine">
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </center>

@stop
