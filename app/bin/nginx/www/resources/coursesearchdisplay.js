function fetch() {
    
    // (B) AJAX SEARCH REQUEST
    var xhr = new XMLHttpRequest();
    xhr.open('GET', "../src/CourseSearchLogic.php", true);
    xhr.onload = function () {
        //there must be no other echos except the JSON file or JSON.parse fails
        var results = JSON.parse(this.response),
            wrapper = document.getElementById("results");
        try
        {
            while (wrapper.removeChild(wrapper.childNodes[0]) !== null)
            {
                //tries to remove all previous search results if they exist
            }
        }
        catch
        {
            //succeeds when it throws exception
        }

        if (results !== null) //using results.length crashed when there was no search results
        {
            wrapper.innerHTML = "";
            for (let res of results) {
                let row = document.createElement("span");

                row.innerHTML = `<form method="post" action="CourseSearchForm.php"><table class="course_search_table"><tr>
                                         <td class="course_search_results_output"><input type="hidden" value="${res['CourseName']}" name="coursename">${res['CourseName']}</input></td> 
                                         <td class="course_search_results_output">${res['CRN']}</td>
                                         <td class="course_search_results_output">${res['Email']}</td> 
                                         <td class="course_search_results_output"><input type="hidden" value="${res['Semester']}" name="semester"><input type="hidden" value="${res['Year']}" name="year">${res['Semester'] + ' ' + res['Year']}</td> 
                                         <td class="course_search_results_output">${res['Location']}</td>
                                         <td class="course_search_results_output"><button name="Enroll" id="Enroll" type="submit">Enroll</button></td>
                                         </tr></table></form>`;
                wrapper.appendChild(row);
            }
        }
        else
        {
            wrapper.innerHTML = "No results found";
        }
    };
    //xhr.send(data);
    try {
        xhr.send();
        if (xhr.status == 200) {
          alert(xhr.response);
        }
      } catch(err) { // instead of onerror
        
        alert("Request failed");
      }

    xhr.onloadend = function() {
        if(xhr.status === 404)
            throw new Error(' replied 404');
    }
    return false;
}

fetch();