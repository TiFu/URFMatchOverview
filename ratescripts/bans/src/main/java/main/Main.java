package main;

import java.io.File;
import java.util.ArrayList;

/**
 * arguments: number of threads followed by path to the match folder (NO spaces).
 *
 */
public class Main {

	public static void main(String[] args) {

		Main m = new Main(Integer.parseInt(args[0]), args[1]);
	}

	public Main(int cores, String folder) {
		File fold = new File(folder);
		File[] files = fold.listFiles();
		
		ArrayList<ArrayList<File>> list = new ArrayList<ArrayList<File>>();
		
		for (int i = 0; i < cores; i++) {
			list.add(new ArrayList<File>());
		}
		
		int counter = 0;
		for (File f: files) {
			if (f.isDirectory()) {
				continue;
			}
			list.get(counter % cores).add(f);
			counter++;
		}
		
		for (ArrayList<File> cur: list) {
			Bans b = new Bans(cur);
			Thread t = new Thread(b);
			t.start();
		}
	}
}
