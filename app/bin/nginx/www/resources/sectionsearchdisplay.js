function fetch() {
    
    // (B) AJAX SEARCH REQUEST
    var xhr = new XMLHttpRequest();
    xhr.open('GET', "../src/SectionSearchLogic.php", true);
    xhr.onload = function () 
    {
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

                //todo: convert start and end time into 24hr AM/PM format

                row.innerHTML = `<form method=\"post\" action=\"../public/CourseEnrollForm.php\"><table class=\"course_enroll_table\"><tr>
                                    <td class=\"enroll_output\">${res['Code']}</td>
                                    <td class=\"enroll_output\">${res['SectionLetter']}</td>
                                    <td class=\"enroll_output\">${res['Email']}</td>
                                    <td class=\"enroll_output\">${res['StartTime']} - ${res['EndTime']}</td>
                                    <td class=\"enroll_output\">${res['Location']}</td>
                                    <input type=\"hidden\" value=\"${res['CRN']}\" name='sectionid' id='sectionid'/>
                                    <td class=\"enroll_output\"><button name=\"Enroll\" id=\"Enroll\" type=\"submit\">Enroll</button></td>
                                    </tr></table></form>`;
                wrapper.appendChild(row);
            }
        }
        else
        {
            wrapper.innerHTML = "An error occurred finding courses.";
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