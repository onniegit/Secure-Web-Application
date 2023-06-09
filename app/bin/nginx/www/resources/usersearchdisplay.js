function fetch() {
    
    // (B) AJAX SEARCH REQUEST
    var xhr = new XMLHttpRequest();

    xhr.open('GET', "../src/UserSearchLogic.php", true);
    
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

            if (results != null) //using results.length crashed when there was no search results
            {
                wrapper.innerHTML = "";
                var fixedDOB = "";
                var DOB = "";
                var fixedStudentYear;
                for (let res of results) 
                {
                    let row = document.createElement("span");
                    if (res['AccType'] === 3) 
                    {
                        if(res['DOB'] !== "") //make sure DOB isn't empty or it will crash
                        {
                            fixedDOB = dateFromUTC(res['DOB'], '-'); //create a Date object using SQLite's format
                            DOB = fixedDOB.getMonth() + '/' + fixedDOB.getDate() + '/' + fixedDOB.getFullYear();
                        }
                        else
                        {
                            DOB = "";
                        }
                        fixedStudentYear = fixStudentYear(res['Year']);
                        row.innerHTML = `<form method="post" action="UserSearchForm.php"><table class="search_table"><tr>
                                         <td class="search_results_output">${res['LName']}, ${res['FName']}</td> 
                                         <td class="search_results_output">${DOB}</td>
                                         <td class="search_results_output" id="email"><input type="hidden" value="${res['Email']}" name="email">${res['Email']}</input></td> 
                                         <td class="search_results_output">${fixedStudentYear}</td>
                                         <td class="search_results_output"><button name="Edit" id="Edit" type="submit">Edit</button></td>
                                         </tr></table></form>`;
                    }
                    else
                    {
                            if(res['DOB'] !== "") //make sure DOB isn't empty or it will crash
                            {
                                fixedDOB = dateFromUTC(res['DOB'], '-'); //create a Date object using SQLite's format
                                DOB = fixedDOB.getMonth() + '/' + fixedDOB.getDate() + '/' + fixedDOB.getFullYear();
                            }
                            else
                            {
                                DOB = "";
                            }
                        row.innerHTML = `<form method="post" action="UserSearchForm.php"><table class="search_table"><tr>
                                         <td class="search_results_output">${res['LName']}, ${res['FName']}</td> 
                                         <td class="search_results_output">${DOB}</td>
                                         <td class="search_results_output" id="email"><input type="hidden" value="${res['Email']}" name="email">${res['Email']}</input></td> 
                                         <td class="search_results_output">${res['Rank']}</td>
                                         <td class="search_results_output"><button name="Edit" id="Edit" type="submit">Edit</button></td>
                                         </tr></table></form>`;
                    }

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
    xhr.onerror = function() {
        alert("Request failed");
    };
      
    return false;
}

function dateFromUTC( dateAsString, ymdDelimiter ) {
    //sqlite pattern is YYYY-MM-DD HH:MM:SS
    var pattern = new RegExp( "(\\d{4})" + ymdDelimiter + "(\\d{2})" + ymdDelimiter + "(\\d{2})" );
    var parts = dateAsString.match( pattern );

    //only gets year month and day from db
    return new Date( Date.UTC(
        parseInt( parts[1] )
        , parseInt( parts[2], 10 )
        , parseInt( parts[3], 10 ) + 1
        , 0
        , 0
        , 0
        , 0
    ));
}

function fixStudentYear(studentYearAsString)
{
    var studentYearAsNumber = parseInt(studentYearAsString);

    if(studentYearAsNumber === 1)
    {
        return "Freshman";
    }
    else if(studentYearAsNumber === 2)
    {
        return "Sophomore";
    }
    else if(studentYearAsNumber === 3)
    {
        return "Junior";
    }
    else
    {
        return "Senior";
    }
}

fetch();