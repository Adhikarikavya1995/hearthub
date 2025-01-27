using UnityEngine;
using UnityEngine.Networking;
using System.Collections;
using Unity.VisualScripting;
using System.IO;

// UnityWebRequest.Get example

// Access a website and use UnityWebRequest.Get to download a page.
// Also try to download a non-existing page. Display the error.

public class DBConn : MonoBehaviour

{
    private string pingUrl = "https://hearthub-post-a0dvbcheceafb5cj.uksouth-01.azurewebsites.net/php_scripts/ping.php"; // PHP API endpoint which unity ping for every 30seconds
    public int machineId; //This must be unique identifier

    // Example data to post (no need for id)
    public string updated_at = System.DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss");
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
    //public string updated_at;


    private string phpUrl = "https://hearthub-post-a0dvbcheceafb5cj.uksouth-01.azurewebsites.net/php_scripts/first_ping.php"; // Replace with your PHP script URL

    public string machineName;
    public string location;


    // to send data to php script to generate qr code and to encode the data
    public string phpURL = "http://localhost/hearthub/qr_code.php"; // Replace with your server's PHP URL

   

    void Start()
    {
        // A correct website page.
        //StartCoroutine(PostRequest());
        //InvokeRepeating("SendPing", 0f, 10f); // to ping database for every 30sec

        //StartCoroutine(SendMachineData(machineName, location));

        StartCoroutine(SendMetricsToGenerateQRCode(compression, recoil, hand_position, rate, machineId));

    }

    void SendPing()
    {
        StartCoroutine(PingServer());
    }

    IEnumerator PostRequest()
    {


        // URL of your PHP script
        string url = "https://hearthub-post-a0dvbcheceafb5cj.uksouth-01.azurewebsites.net/php_scripts/post_request.php";

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
        form.AddField("updated_at", updated_at);


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
                Debug.Log("Please start XAMPP server");
            }
            else
            {
                Debug.Log("ping status sent successfully");
            }
        }
    }



    IEnumerator SendMachineData(string machineName, string location)
    {
        // Prepare form data
        WWWForm form = new WWWForm();
        form.AddField("machine_name", machineName);
        form.AddField("location", location);

        using (UnityWebRequest request = UnityWebRequest.Post(phpUrl, form))
        {
            // Send the request and wait for the response
            yield return request.SendWebRequest();

            if (request.result == UnityWebRequest.Result.ConnectionError || request.result == UnityWebRequest.Result.ProtocolError)
            {
                Debug.LogError($"Error sending data: {request.error}");
            }
            else
            {
                // Get the response as plain text
                string response = request.downloadHandler.text;

                if (response.StartsWith("Error") || response.Contains("Machine name already exists"))
                {
                    Debug.LogError($"Server error: {response}");
                }
                else
                {
                    // Save the machine ID to a file
                    string filePath = @"C:/machine_id.txt";

                    File.WriteAllText(filePath, $"{response}");

                    Debug.Log($"Machine ID saved to {filePath}");

                }
            }
        }
    }


    IEnumerator SendMetricsToGenerateQRCode(int compression, int recoil, int hand_position, int rate, int machineId)
    {
        WWWForm form = new WWWForm();
        form.AddField("compression", compression.ToString());
        form.AddField("recoil", recoil.ToString());
        form.AddField("hand_position", hand_position.ToString());
        form.AddField("rate", rate.ToString());
        form.AddField("machine_id", machineId.ToString());

        using (UnityWebRequest www = UnityWebRequest.Post(phpURL, form))
        {
            yield return www.SendWebRequest();

            if (www.result == UnityWebRequest.Result.Success)
            {
                // Parse the response to get the encoded URL
                string jsonResponse = www.downloadHandler.text;
                var response = JsonUtility.FromJson<PHPResponse>(jsonResponse);
                string encodedUrl = response.encoded_url;

                // Print the encoded URL for debugging
                Debug.Log("Encoded URL received from PHP: " + encodedUrl);
            }
            else
            {
                Debug.LogError("Failed to send metrics: " + www.error);
            }
        }
    }

    [System.Serializable]
    public class PHPResponse
    {
        public string encoded_url;
    }



}
