
function changeDegree(newDegree, lessonId, subjectIds) {
    $.ajax({
        type: 'post',
        url: 'getLessonsByChangingDegree',
        data: {
            degreeId : newDegree
        },
        success: function (response) {
            document.getElementById(lessonId).innerHTML = response;
            if(subjectIds.length > 0)
                changeLesson(document.getElementById(lessonId).value, subjectIds);

        }
    });
}

function changeDegreeWithSelectedLesson(newDegree, lessonId, subjectIds, compassIds, grades, selectedLesson, boxId) {
    $.ajax({
        type: 'post',
        url: 'getLessonsByChangingDegreeWithSelectedLesson',
        data: {
            degreeId : newDegree,
            selectedLesson : selectedLesson
        },
        success: function (response) {
            document.getElementById(lessonId).innerHTML = response;

            if(subjectIds.length > 0) {
                changeLessonAndBox(document.getElementById(lessonId).value, subjectIds, compassIds, grades, boxId);
            }
        }
    });
}

function changeLesson(newLesson, subjectIds) {
    $.ajax({
        type: 'post',
        url: 'getSubjectsByChangingLesson',
        data: {
            lessonId : newLesson
        },
        success: function (response) {
            for(i = 0; i < subjectIds.length; i++) {
                document.getElementById(subjectIds[i]).innerHTML = response;
            }

        }
    });
}

function changeLessonAndBox(newLesson, subjectIds, compassIds, grades, boxId) {
    $.ajax({
        type: 'post',
        url: 'getSubjectsByChangingLesson',
        data: {
            lessonId : newLesson
        },
        success: function (response) {
            for(i = 0; i < subjectIds.length; i++) {
                document.getElementById(subjectIds[i]).innerHTML = response;
            }
            getBoxItems(boxId, subjectIds, compassIds, grades);
        }
    });
}

function showBoxItems(boxId, itemId) {
    $.ajax({
        type: 'post',
        url: 'getBoxItemsByNames',
        data: {
            boxId : boxId
        },
        success: function (response) {
            tmp = JSON.parse(response);
            newElement = "";
            for(i = 0; i < tmp.length; i++) {
                newElement += "<div class='col-xs-12'><label><span style='margin-left: 5px; margin-right: 5px'> نام مبحث</span><input disabled type='text' value='" + tmp[i].subject_id + "'></label>";
                newElement += "<label><span style='margin-left: 5px; margin-right: 5px'>نام حیطه </span><input disabled type='text' value='" + tmp[i].compassId + "'></label>";
                newElement += "<label><span style='margin-left: 5px; margin-right: 5px'>سطح سختی </span><input type='text' disabled value='" + tmp[i].grade + "'></label></div>";
            }
            document.getElementById(itemId).innerHTML = newElement;
        }
    });
}

function deleteSelectedBox(boxId) {
    $.ajax({
        type: 'post',
        url: 'deleteBox',
        data: {
            boxId : boxId
        },
        success: function (response) {
            document.location.href = 'seeBoxes';
        }
    });
}

function updateBox(from, to, subjectIds, grades, compassIds, boxName, boxId) {

    $.ajax({
        type: 'post',
        url: 'updateBox',
        data: {
            from : from,
            to : to,
            subjectIds : subjectIds,
            grades : grades,
            compassIds : compassIds,
            boxName : boxName,
            boxId : boxId
        },
        success: function (response) {
            if(response == -1) {
                alert("نام جعبه در سیستم موجود است");
            }
            else {
                alert("جعبه ی مورد نظر با موفقیت ویرایش شد");
                document.location.href = 'seeBoxes';
            }
        },
        error: function () {
            alert("اشکالی در برقراری ارتباط با سرور به وجود آمده است");
        }
    });
}

function addBox(from, to, subjectIds, grades, compassIds, boxName) {
    $.ajax({
        type: 'post',
        url: 'addNewBox',
        data: {
            from : from,
            to : to,
            subjectIds : subjectIds,
            grades : grades,
            compassIds : compassIds,
            boxName : boxName
        },
        success: function (response) {
            if(response == -1) {
                alert("نام جعبه در سیستم موجود است");
            }
            else {
                alert("جعبه ی مورد نظر با موفقیت ایجاد شد");
                document.location.href = 'createBox';
            }
        },
        error: function () {
            alert("اشکالی در برقراری ارتباط با سرور به وجود آمده است");
        }
    });
}

function getBoxItems(boxId, subjectIds, compassIds, gradeIds) {

    $.ajax({
        type: 'post',
        url: 'getBoxItems',
        data: {
            boxId : boxId
        },
        success: function (response) {
            tmp = JSON.parse(response);
            for(i = 0; i < tmp.length; i++) {
                document.getElementById(subjectIds[i]).value = tmp[i].subject_id;
                document.getElementById(compassIds[i]).value = tmp[i].compassId;
                document.getElementById(gradeIds[i]).value = tmp[i].grade;
            }
        }
    });
}

function changeQuiz(qId, name, author, numQ, startTime, startDate, endTime, endDate, timeLen) {
    $.ajax({
        type : 'post',
        url : 'changeQuiz',
        data : {
          qId : qId
        },
        success : function (response) {
            result = JSON.parse(response);
            tmp = result[0];
            document.getElementById(name).innerHTML = "نام ارزیابی : " + tmp.QN;
            // document.getElementById(author).innerHTML = "طراح ارزیابی : " + tmp.author;
            document.getElementById(numQ).innerHTML = "تعداد سوالات : " + result[1];
            sDate = tmp.sDate;
            sDate = sDate[0] + sDate[1] + sDate[2] + sDate[3] + "/" + sDate[4] + sDate[5] + "/" + sDate[6] + sDate[7];
            sTime = tmp.sTime;
            sTime = sTime[2] + sTime[3] + " : " + sTime[0] + sTime[1];
            eDate = tmp.eDate;
            eDate = eDate[0] + eDate[1] + eDate[2] + eDate[3] + "/" + eDate[4] + eDate[5] + "/" + eDate[6] + eDate[7];
            eTime = tmp.eTime;
            eTime = eTime[2] + eTime[3] + " : " + eTime[0] + eTime[1];
            document.getElementById(startTime).innerHTML = "زمان شروع ارزیابی : " + sTime;
            document.getElementById(startDate).innerHTML = "تاریخ شروع ارزیابی : " + sDate;
            document.getElementById(endTime).innerHTML = "زمان خاتمه ارزیابی : " + eTime;
            document.getElementById(endDate).innerHTML = "تاریخ خاتمه ارزیابی : " + eDate;
            document.getElementById(timeLen).innerHTML = "مدت زمان پاسخ گویی : " + tmp.tL;
        }
    });
}

function getCompasses(compassIds) {
    $.ajax({
        type: 'post',
        url: 'getCompasses',
        success: function (response) {

            for(i = 0; i < compassIds.length; i++) {
                document.getElementById(compassIds[i]).innerHTML = response;
            }
            
            
        }
    });
}

function getTotalQ(subject_id, compassId, level) {

    $.ajax({
        type: 'post',
        url : 'getTotalQ',
        data: {
            subject_id: subject_id,
            compassId: compassId,
            level: level
        },
        success: function (response) {
            alert(response);
            // $("#" + element).text(response);
        }
    });
    
}