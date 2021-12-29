package com.charlesTech8;

import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLEncoder;

public class Client extends AppCompatActivity {
    private EditText _nom, _prenom, _adresse, _email, _telephone, _mdp;
    private Button btncreer;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_client);

        _nom = (EditText) findViewById(R.id.nom);
        _prenom = (EditText) findViewById(R.id.prenom);
        _adresse = (EditText) findViewById(R.id.adresse);
        _email = (EditText) findViewById(R.id.email);
        _telephone = (EditText) findViewById(R.id.phone);
        _mdp = (EditText) findViewById(R.id.mdp);

        btncreer = (Button) findViewById(R.id.btncreer);
        btncreer.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String nom = _nom.getText().toString().trim();
                String prenom = _prenom.getText().toString().trim();
                String adresse = _adresse.getText().toString().trim();
                String email = _email.getText().toString().trim();
                String telephone = _telephone.getText().toString().trim();
                String mdp = _mdp.getText().toString().trim();

                Client.bg background = new Client.bg(Client.this);
                background.execute(nom, prenom, adresse, email, telephone, mdp);
            }
        });
    }
    private class bg extends AsyncTask<String,Void,String> {
        AlertDialog dialog;
        Context c;
        public bg(Context context){
            this.c = context;
        }

        @Override
        protected void onPreExecute() {

            dialog = new AlertDialog.Builder(c).create();
            dialog.setTitle("Etat");
        }

        @Override
        protected String doInBackground(String... strings) {
            String result  ="";
            String nom = strings[0];
            String prenom = strings[1];
            String adresse = strings[2];
            String email = strings[3];
            String telephone = strings[4];
            String mdp = strings[5];
            ConnexionBd ConBd = new ConnexionBd();
            String connstr = ConBd.valCon("client_insc.php");

            try {
                URL url = new URL(connstr);
                HttpURLConnection http = (HttpURLConnection) url.openConnection();
                http.setRequestMethod("POST");
                http.setDoInput(true);
                http.setDoOutput(true);
                OutputStream ops = http.getOutputStream();
                BufferedWriter writer = new BufferedWriter(new OutputStreamWriter(ops,"UTF-8"));
                String data = URLEncoder.encode("nom","UTF-8") + "=" + URLEncoder.encode(nom,"UTF-8") +
                        "&&" + URLEncoder.encode("prenom", "UTF-8")+ "=" + URLEncoder.encode(prenom,"UTF-8")+
                        "&&" + URLEncoder.encode("adresse", "UTF-8")+ "=" + URLEncoder.encode(adresse,"UTF-8")+
                        "&&" + URLEncoder.encode("email", "UTF-8")+ "=" + URLEncoder.encode(email,"UTF-8")+
                        "&&" + URLEncoder.encode("telephone", "UTF-8")+ "=" + URLEncoder.encode(telephone,"UTF-8")+
                        "&&" + URLEncoder.encode("pwd", "UTF-8")+ "=" + URLEncoder.encode(mdp,"UTF-8");
                writer.write(data);
                writer.flush();
                writer.close();
                InputStream ips = http.getInputStream();
                BufferedReader reader = new BufferedReader(new InputStreamReader(ips, "UTF-8"));
                String ligne ="";
                while ((ligne = reader.readLine())!= null){
                    result = result + ligne;
                }
                reader.close();
                ips.close();
                http.disconnect();

                return result;
            }catch (MalformedURLException e) {
                e.printStackTrace();
            } catch (IOException e) {
                e.printStackTrace();
                Log.e("error",e.getMessage());
            }
            return result;
        }

        @Override
        protected void onPostExecute(String s) {
            dialog.setMessage(s);
            try {
                dialog.show();
            } catch (Exception e){
                Log.e("errorpost",e.getMessage());
            }

            if (s.contains("Inscription succes")){
                _nom.setText("");
                _prenom.setText("");
                _adresse.setText("");
                _email.setText("");
                _telephone.setText("");
                _mdp.setText("");
            }
        }
    }
}