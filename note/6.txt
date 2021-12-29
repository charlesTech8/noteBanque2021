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

public class Transfert extends AppCompatActivity {
    EditText _numeroCompte1, _numeroCompte2, _solde, _pass;
    Button _btntransfert;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_transfert);

        _numeroCompte1 = (EditText) findViewById(R.id.sender);
        _numeroCompte2 = (EditText) findViewById(R.id.receveur);
        _solde = (EditText) findViewById(R.id.montant);
        _pass = (EditText) findViewById(R.id.password);
        _btntransfert = (Button) findViewById(R.id.validerbtn);

        _btntransfert.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String numeroCompte1 = _numeroCompte1.getText().toString().trim();
                String numeroCompte2 = _numeroCompte2.getText().toString().trim();
                String solde = _solde.getText().toString().trim();
                String pass = _pass.getText().toString().trim();

                Transfert.bg background = new Transfert.bg(Transfert.this);
                background.execute(numeroCompte1, numeroCompte2, solde, pass);
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
            String numeroCompte1 = strings[0];
            String numeroCompte2 = strings[1];
            String solde = strings[2];
            String pass = strings[3];
            ConnexionBd ConBd = new ConnexionBd();
            String connstr = ConBd.valCon("transfert.php");

            try {
                URL url = new URL(connstr);
                HttpURLConnection http = (HttpURLConnection) url.openConnection();
                http.setRequestMethod("POST");
                http.setDoInput(true);
                http.setDoOutput(true);
                OutputStream ops = http.getOutputStream();
                BufferedWriter writer = new BufferedWriter(new OutputStreamWriter(ops,"UTF-8"));
                String data = URLEncoder.encode("numeroCompte1","UTF-8") + "=" + URLEncoder.encode(numeroCompte1,"UTF-8") +
                        "&&" + URLEncoder.encode("numeroCompte2", "UTF-8")+ "=" + URLEncoder.encode(numeroCompte2,"UTF-8")+
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
            _numeroCompte1.setText("");
            _numeroCompte2.setText("");
            _solde.setText("");
            _pass.setText("");
        }
    }
}