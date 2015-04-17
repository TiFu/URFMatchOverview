package analyze;

import java.io.File;
import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.StandardCopyOption;
import java.util.ArrayList;
import java.util.List;

/**
 * 
 * Args: number of Threads followed by a path to the match files (NO spaces).
 */
public class Main {
	
	private static final int FILES_PER_DIR = 10; // only 25 per dir

	public static void main(String[] args) {
		if (args.length != 2) {
			System.out.println("Wrong arguments");
			return;
		}
		Main m = new Main(Integer.parseInt(args[0]), args[1]);
	}

	public Main(int cores, String folder) {
		File f = new File(folder);
		if (!f.isDirectory()) {
			System.out.println("f is no directory");
			return;
		}

		List<List<File>> files = new ArrayList<List<File>>();

		for (int i = 0; i < cores; i++) {
			files.add(new ArrayList<File>());
		}

		File currentDir = null;
		int counter = 0;
		for (File current : f.listFiles()) {
			if (counter % Main.FILES_PER_DIR == 0) {
				File dir = new File(folder + "/" + (counter / Main.FILES_PER_DIR));
				currentDir = dir;
				files.get((counter / Main.FILES_PER_DIR) % cores).add(currentDir);
				if (!dir.exists()) {
					dir.mkdir();
				}
			}
			if (current.isDirectory()) {
				continue;
			}
			File dest = new File(currentDir.getAbsolutePath() + "/"
					+ current.getName());
			try {
				Files.move(current.toPath(), dest.toPath(),
						StandardCopyOption.REPLACE_EXISTING);
			} catch (IOException e) {
				System.out.println("Couln't copy file: " + current.getName());
				e.printStackTrace();
			}
			//
			counter++;
		}
		System.out.println("-----------------------------------------------");
		int currentCore = 0;
		for (List<File> list : files) {
			System.out.println("Core: " + currentCore + ", Dirs: " + list.size());
			this.analyze(list);
			currentCore++;
		}
	}

	public void analyze(List<File> list) {
		Analyzer a = new Analyzer(list);
		Thread t = new Thread(a);
		t.start();
	}

}
