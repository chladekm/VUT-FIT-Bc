/******************************************************************************
 * Laborator 01 - Zaklady pocitacove grafiky - IZG
 * ihulik@fit.vutbr.cz
 *
 * $Id: $
 * 
 * Popis: Hlavicky funkci pro funkce studentu
 *
 * Opravy a modifikace:
 * - ibobak@fit.vutbr.cz, orderedDithering
 */

#include "student.h"
#include "globals.h"

#include <time.h>

const int M[] = {
    0, 204, 51, 255,
    68, 136, 187, 119,
    34, 238, 17, 221,
    170, 102, 153, 85
};

const int M_SIDE = 4;

/******************************************************************************
 ******************************************************************************
 Funkce vraci pixel z pozice x, y. Je nutne hlidat frame_bufferu, pokud 
 je dana souradnice mimo hranice, funkce vraci barvu (0, 0, 0).
 Ukol za 0.25 bodu */
S_RGBA getPixel(int x, int y)
{
    // todo
	S_RGBA pixel;

	pixel = COLOR_BLACK; //implicitni barva (0, 0, 0)

	if ((x >= 0) && (y >= 0) && (x <= width) && (y <= height))
		pixel = *(frame_buffer + width * y + x);

	return pixel;
}
/******************************************************************************
 ******************************************************************************
 Funkce vlozi pixel na pozici x, y. Je nutne hlidat frame_bufferu, pokud 
 je dana souradnice mimo hranice, funkce neprovadi zadnou zmenu.
 Ukol za 0.25 bodu */
void putPixel(int x, int y, S_RGBA color)
{
    // todo

	if ((x >= 0 && y >= 0) && (x <= width && y <= height))
		*(frame_buffer + width * y + x) = color;

}
/******************************************************************************
 ******************************************************************************
 Funkce prevadi obrazek na odstiny sedi. Vyuziva funkce GetPixel a PutPixel.
 Ukol za 0.5 bodu */

// Volam pomoci G
void grayScale()
{
	S_RGBA pixel;

	for (int  x = 0; x < width; x++)
	{
		for (int y = 0; y < height; y++)
		{
			pixel = getPixel(x, y);

			int color = ROUND(0.299 * pixel.red + 0.587 * pixel.green + 0.114 * pixel.blue);

			pixel.red = color;
			pixel.green = color;
			pixel.blue = color;
			
			putPixel(x, y, pixel);
		}
	}
	
    // todo
}

/******************************************************************************
 ******************************************************************************
 Funkce prevadi obrazek na cernobily pomoci algoritmu maticoveho rozptyleni.
 Ukol za 1 bod */

// Volam pomoci M
void orderedDithering()
{
    // todo
	grayScale();

	S_RGBA pixel;

	for (int x = 0; x < width; x++)
	{
		for (int y = 0; y < height; y++)
		{
			pixel = getPixel(x,y);

			int i = x % M_SIDE;
			int j = y % M_SIDE;

			if (pixel.red > M[j*M_SIDE + i])
				putPixel(x, y, COLOR_WHITE);
			else
				putPixel(x, y, COLOR_BLACK);

		}


	}
}

/******************************************************************************
 ******************************************************************************
 Funkce prevadi obrazek na cernobily pomoci algoritmu distribuce chyby.
 Ukol za 1 bod */

errorHelpFunction(double error, int x, int y)
{
	S_RGBA pixel;

	pixel = getPixel(x, y);

	int newColor = ROUND(pixel.red + error);

	if (newColor > 255)
		newColor = 255;

	if (newColor < 0)
		newColor = 0;

	pixel.red = newColor;
	pixel.green = newColor;
	pixel.blue = newColor;

	putPixel(x, y, pixel);
}

//Volam D
void errorDistribution()
{   

	S_RGBA pixel;

	grayScale();

	for (int  x = 0; x < width - 1; x++)
	{
		for (int  y = 0; y < height - 1; y++)
		{
			pixel = getPixel(x, y);

			int origBarva = pixel.red;

			if (pixel.red > 127)
				pixel = COLOR_WHITE;
			else
				pixel = COLOR_BLACK;

			double error = origBarva - pixel.red;
			
			errorHelpFunction(error * 3.0 / 8.0, x, y + 1);
			errorHelpFunction(error * 3.0 / 8.0, x + 1, y);
			errorHelpFunction(error * 2.0 / 8.0, x + 1, y + 1);

			putPixel(x, y, pixel);
		}
	}

}

/******************************************************************************
 ******************************************************************************
 Funkce prevadi obrazek na cernobily pomoci metody prahovani.
 Demonstracni funkce */
void thresholding(int Threshold)
{
	/* Prevedeme obrazek na grayscale */
	grayScale();

	/* Projdeme vsechny pixely obrazku */
	for (int y = 0; y < height; ++y)
		for (int x = 0; x < width; ++x)
		{
			/* Nacteme soucasnou barvu */
			S_RGBA color = getPixel(x, y);

			/* Porovname hodnotu cervene barevne slozky s prahem.
			   Muzeme vyuzit jakoukoli slozku (R, G, B), protoze
			   obrazek je sedotonovy, takze R=G=B */
			if (color.red > Threshold)
				putPixel(x, y, COLOR_WHITE);
			else
				putPixel(x, y, COLOR_BLACK);
		}
}

/******************************************************************************
 ******************************************************************************
 Funkce prevadi obrazek na cernobily pomoci nahodneho rozptyleni. 
 Vyuziva funkce GetPixel, PutPixel a GrayScale.
 Demonstracni funkce. */
void randomDithering()
{
	/* Prevedeme obrazek na grayscale */
	grayScale();

	/* Inicializace generatoru pseudonahodnych cisel */
	srand((unsigned int)time(NULL));

	/* Projdeme vsechny pixely obrazku */
	for (int y = 0; y < height; ++y)
		for (int x = 0; x < width; ++x)
		{
			/* Nacteme soucasnou barvu */
			S_RGBA color = getPixel(x, y);
			
			/* Porovname hodnotu cervene barevne slozky s nahodnym prahem.
			   Muzeme vyuzit jakoukoli slozku (R, G, B), protoze
			   obrazek je sedotonovy, takze R=G=B */
			if (color.red > rand()%255)
			{
				putPixel(x, y, COLOR_WHITE);
			}
			else
				putPixel(x, y, COLOR_BLACK);
		}
}
/*****************************************************************************/
/*****************************************************************************/