Just quizzing
===============================================
A simple and minimalistic quiz platform created in PHP and SQLite

Requirements
------------

- PHP 5.1.2+
- SQLite3
- a web server (Apache/Nginx/IIS/etc.)

Project Page
-----------
http://blog.claudiupersoiu.ro/just-quizzing/

Demo
----
http://claudiu-persoiu.github.io/Just-Quizzing-Server/demo/

Installation
------------

- Copy the files to the server public folder (usually *www* or *htdocs*);
- Make sure the web server have write permissions to db.info file and questions-img folder;
- Run *yourdomain/admin.php*, with user "admin" and password "admin" (without quotes);
- Delete *upgrade-db.php*

Upgrade
------

- Backup the *data* folder in a safe location;
- Overwrite the files with the new version;
- Copy the data folder back in the quiz and overwrite with your saved files;
- Make sure the web server have write permissions to *db.info* file and *questions-img* folder;
- Open *yourdomain/update-db.php* to update the database, if there aren't any issues you should see *Update complete*;
- After the update has run successfully delete *update-db.php*.

Debug
-----

If you see a black screen you can try to modify:

    ini_set('display_errors', '1');

inside *includes/config.php*.

Self processing
---------------

When entering a new question you have the option to *process* a text into question elements.

1. Add to the "Input Data" column the demo data below:

        What animal is in the picture below?
        *A - giraffe
        B - dog
        C - snake
        D - cow

2. In "Input Script"

        var contentArray = content.split("\n");
        var question = contentArray[0];
        var questions = [];
        var answers = [];
        var tmp;

        for(var i = 1; i < contentArray.length; i++) {
            tmp = contentArray[i];
            questions.push(tmp.substr(4).trim());

            if(tmp.substr(0, 1) == '*') {
                answers.push(true);
            } else {
                answers.push(false);
            }
        }

3. Click "process data". This will process the text from the input to the fields in the form above. Now all you have to do is click "add question".

This is specific for the input type. 

In this particular case the first line in the script is splitting the text into lines.
Lines 2-4 are just defining variables that will be used later. Question will store the actual question which is at the first line (0 for is the first item in JavaScript). Questions will hold the possible answers and answers will contain the correct one (true for correct/false for incorrect).

Line 6 is iterating over the remaining lines (starting from the second, because the first was 0) and adding them to the questions array.

Line 7 is getting the current line and save it into an array.

Line 8 is adding the answer to the questions array starting from the 5th element of the string, to eliminate "*A - " from the string.

Line 10 is getting the first character of the current line to see if it is "*", which in this particular case means the answer is correct.

Line 11-14 is adding true/false to the answers array to mark it as correct or wrong.

----

DISCLAIMER

 IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
