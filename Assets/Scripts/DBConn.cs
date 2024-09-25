using UnityEngine;
using UnityEngine.Networking;
using System.Collections;

// UnityWebRequest.Get example

// Access a website and use UnityWebRequest.Get to download a page.
// Also try to download a non-existing page. Display the error.

public class DBConn : MonoBehaviour

{

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
}