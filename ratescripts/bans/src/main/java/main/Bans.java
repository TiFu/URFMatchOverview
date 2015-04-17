package main;

import java.io.BufferedReader;
import java.io.File;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.util.ArrayList;

public class Bans implements Runnable {
	private ArrayList<File> files;

	public Bans(ArrayList<File> files) {
		this.files = files;
	}

	public void run() {
		int counter = 1;
		for (File f : this.files) {
			URL url;
			try {
				url = new URL(
						"http://localhost/challenge/ratescripts/new/bans.php?file=file:///"
								+ f.getAbsolutePath());
			} catch (MalformedURLException e) {
				System.out.println("MalformedURLException");
				return;
			}
			try {
				URLConnection con = url.openConnection();
				BufferedReader in = new BufferedReader(new InputStreamReader(
						con.getInputStream()));
				String inputLine;

				while ((inputLine = in.readLine()) != null) {
				}
				in.close();
			} catch (IOException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			System.out.println(counter + "/" + this.files.size());
			counter++;
		}
		System.out.println("Analyzer Thread finished");
	}
}