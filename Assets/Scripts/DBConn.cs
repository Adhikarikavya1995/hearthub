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
    public string timestamp = "2024-09-25 12:34:56";
    public int compression = 39;
    public int recoil = 70;
    public int handposition = 58;
    public int overall_score = 85;
    public string feedback = "Excellent";

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

            form.AddField("timestamp", timestamp);
            form.AddField("compression", compression.ToString());
            form.AddField("recoil", recoil.ToString());
            form.AddField("handposition", handposition.ToString());
            form.AddField("overall_score", overall_score.ToString());
            form.AddField("feedback", feedback);

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
            }
            else
            {
                Debug.Log("ping status sent successfully");
            }
        }
    }
}