<script>
    $(document).ready(function(){
        $('.clockpicker').clockpicker();
    });
</script>

<div class="myRegister">
    <div class="row data">
        <center>
            <h3>اطلاعات آزمون</h3>
        </center>
        <div class="line"></div>

        <form method="post" action="{{URL('createQuiz')}}">
            <center>
                <div class="col-xs-12">
                    <label>
                        <span>نام آزمون</span>
                        <input type="text" name="name" value="{{$qName}}" maxlength="40" required autofocus>
                    </label>
                </div>
                <div class="col-xs-12">
                    <label>
                        <span>مدت آزمون بر حسب دقیقه</span>
                        <input type="number" name="timeLen" value="{{$timeLen}}" min="0" max="999">
                    </label>
                </div>
                <div class="col-xs-12">
                    <label>
                        <span>تاریخ شروع آزمون</span>
                        <input type="button" style="border: none; width: 30px; height: 30px; background: url({{ URL::asset('images/calendar-flat.png') }}) repeat 0 0; background-size: 100% 100%;" id="date_btn">
                        <br/>
                        <input type="text" id="date_input" name="sDate" value="{{$sDate}}" required readonly>
                        <script>
                            Calendar.setup({
                                inputField: "date_input",
                                button: "date_btn",
                                ifFormat: "%Y/%m/%d",
                                dateType: "jalali"
                            });
                        </script>
                    </label>
                </div>
                <div class="col-xs-12">
                    <label>
                        <span>ساعت شروع آزمون</span>
                        <div class="clockpicker">
                            <input type="text" style="width: 100%" name="sTime" class="form-control" required value="{{$sTime}}">
                        </div>
                    </label>
                </div>
                <div class="col-xs-12">
                    <label>
                        <span>تاریخ اتمام آزمون</span>
                        <input type="button" style="width: 30px; height: 30px; border: none; background: url({{ URL::asset('images/calendar-flat.png') }}) repeat 0 0; background-size: 100% 100%;" id="date_btn2">
                        <br/>
                        <input type="text" id="date_input2" value="{{$eDate}}" name="eDate" required readonly>
                        <script>
                            Calendar.setup({
                                inputField: "date_input2",
                                button: "date_btn2",
                                ifFormat: "%Y/%m/%d",
                                dateType: "jalali"
                            });
                        </script>
                    </label>
                </div>
                <div class="col-xs-12">
                    <label>
                        <span>ساعت اتمام آزمون</span>
                        <div class="clockpicker">
                            <input type="text" name="eTime" style="width: 100%" class="form-control" required value="{{$eTime}}">
                        </div>
                    </label>
                </div>
                <div class="col-xs-12">
                    <label>
                        <span>نوع سوالات</span>
                        <select name="kindQ">
                            <option value="1">تستی</option>
                            @if($kindQ == 2)
                                <option selected value="2">کوتاه پاسخ</option>
                                <option value="3">تلفیقی</option>
                            @elseif($kindQ == 3)
                                <option value="2">کوتاه پاسخ</option>
                                <option selected value="3">تلفیقی</option>
                            @else
                                <option value="2">کوتاه پاسخ</option>
                                <option value="3">تلفیقی</option>
                            @endif
                        </select>
                    </label>
                </div>
                <div class="col-xs-12">
                    <label>
                        <span>نمره از</span>
                        <input type="number" name="mark" max="100" min="0" value="{{$mark}}">
                    </label>
                </div>
                <div class="col-xs-12">
                    <label>
                        <span>آزمون نمره ی منفی داشته باشد</span>
                        @if($minusMark)
                            <input type="checkbox" checked name="minusMark">
                        @else
                            <input type="checkbox" name="minusMark">
                        @endif
                    </label>
                </div>
                <p style="margin-top: 5px; color: red">{{$error}}</p>
                <input type="submit" class="MyBtn" style="width: auto; padding: 5px; margin-top: 30px" name="submitQ" value="مرحله ی بعد">
            </center>
        </form>
    </div>
</div>