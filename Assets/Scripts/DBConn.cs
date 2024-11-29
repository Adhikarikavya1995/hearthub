using UnityEngine;
using UnityEngine.Networking;
using System.Collections;
using Unity.VisualScripting;

// UnityWebRequest.Get example

// Access a website and use UnityWebRequest.Get to download a page.
// Also try to download a non-existing page. Display the error.

public class DBConn : MonoBehaviour

{
    private string pingUrl = "http://localhost/hearthub/ping.php"; // PHP API endpoint which unity ping for every 30seconds
    public int machineId = 1; //This must be unique identifier

    // Example data to post (no need for id)
    //public string timestamp = "2024-09-25 12:34:56";
    public int time_engaged;
    public int distance_while_active;
    public int hand_position;
    public int rate;
    public int compression;
    public int recoil;
    public string watched_animation;
    public int sessions_played;
    public string question_1_response;
    public string question_2_response;
    public string session_status;
 

    void Start()
    {
        // A correct website page.
        StartCoroutine(PostRequest());
        InvokeRepeating("SendPing", 0f, 10f); // to ping database for every 30sec

    }

    void SendPing()
    {
        StartCoroutine(PingServer());
    }

    IEnumerator PostRequest()
    {
      

            // URL of your PHP script
            string url = "http://localhost/hearthub/post_request.php";

            // form to send data
            WWWForm form = new WWWForm();

        //form.AddField("timestamp", timestamp);
        form.AddField("machine_id", machineId.ToString());
        form.AddField("time_engaged", time_engaged.ToString());
        form.AddField("distance_while_active", distance_while_active.ToString());
        form.AddField("hand_position", hand_position.ToString());
        form.AddField("rate", rate.ToString());
        form.AddField("compression", compression.ToString());
        form.AddField("recoil", recoil.ToString());
        form.AddField("watched_animation", watched_animation);
        form.AddField("sessions_played", sessions_played.ToString());
        form.AddField("question_1_response", question_1_response);
        form.AddField("question_2_response", question_2_response);
        form.AddField("session_status", session_status);
        
       


        // Create a UnityWebRequest for POST
        UnityWebRequest webRequest = UnityWebRequest.Post(url, form);

            // Request and wait for the desired page.
            yield return webRequest.SendWebRequest();

            if (webRequest.result == UnityWebRequest.Result.ConnectionError || webRequest.result == UnityWebRequest.Result.ProtocolError)
            {
                Debug.LogError("Error: " + webRequest.error);
            }
            else
            {
                // Read and display the response from the PHP file
      
                Debug.Log("Response from PHP: " + webRequest.downloadHandler.text);
                Debug.Log("Php response error");
        }



    }



    IEnumerator PingServer()
    {

        WWWForm form = new WWWForm();
        form.AddField("machine_id", machineId);
        form.AddField("timestamp", System.DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss"));

        using (UnityWebRequest www = UnityWebRequest.Post(pingUrl, form))
        {
            yield return www.SendWebRequest();

            if (www.result != UnityWebRequest.Result.Success)
            {
                Debug.Log(www.error);
                Debug.Log("Please start XAMPP server");
            }
            else
            {
                Debug.Log("ping status sent successfully");
            }
        }
    }
}