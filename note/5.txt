package com.charlesTech8;

import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;

import android.content.Context;
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

public class Retrait extends AppCompatActivity {
    EditText _numeroCompte, _solde, _pass;
    Button _btnretrait;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_retrait);

        _numeroCompte = (EditText) findViewById(R.id.numerocompte);
        _solde = (EditText) findViewById(R.id.montant);
        _pass = (EditText) findViewById(R.id.password);
        _btnretrait = (Button) findViewById(R.id.soldebtn);

        _btnretrait.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String numeroCompte = _numeroCompte.getText().toString().trim();
                String solde = _solde.getText().toString().trim();
                String pass = _pass.getText().toString().trim();

                Retrait.bg background = new Retrait.bg(Retrait.this);
                background.execute(numeroCompte,solde, pass);
            }
        });
    }
    private class bg extends AsyncTask<String,Void,String>{
        AlertDialog dialog;
        Context c;
        public bg(Context context){
            this.c = context;
        }

        @Override
        protected void onPreExecute() {

            dialog = new AlertDialog.Builder(c).create();
            dialog.setTitle("Etat : ");
        }

        @Override
        protected String doInBackground(String... strings) {
            String result  ="";
            String numeroCompte = strings[0];
            String solde = strings[1];
            String pass = strings[2];
            ConnexionBd ConBd = new ConnexionBd();
            String connstr = ConBd.valCon("retrait.php");

            try{
                URL url = new URL(connstr);
                HttpURLConnection http = (HttpURLConnection) url.openConnection();
                http.setRequestMethod("POST");
                http.setDoInput(true);
                http.setDoOutput(true);
                OutputStream ops = http.getOutputStream();
                BufferedWriter writer = new BufferedWriter(new OutputStreamWriter(ops,"UTF-8"));
                String data = URLEncoder.encode("numeroCompte","UTF-8") + "=" + URLEncoder.encode(numeroCompte,"UTF-8") +
                        "&&" + URLEncoder.encode("solde", "UTF-8")+ "=" + URLEncoder.encode(solde,"UTF-8")+
                        "&&" + URLEncoder.encode("pass", "UTF-8")+ "=" + URLEncoder.encode(pass,"UTF-8");
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

            } catch (MalformedURLException e) {
                e.printStackTrace();
            } catch (IOException e) {
                e.printStackTrace();
                Log.e("error",e.getMessage());
            }
            return result;
        }

        protected void onPostExecute(String s) {
            dialog.setMessage(s);
            try {
                dialog.show();
            } catch (Exception e){
                Log.e("errorpost",e.getMessage());
            }
            _numeroCompte.setText("");
            _solde.setText("");
            _pass.setText("");
        }
    }
}