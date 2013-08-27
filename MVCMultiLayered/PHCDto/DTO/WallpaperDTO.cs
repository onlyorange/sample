using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace PHCDto.DTO
{
    public class WallpaperDTO
    {
        public int WID { get; set; }
        public string Wallpaper { get; set; }
        public string Title { get; set; }
        public int BadgesNeeded { get; set; }
        public DateTime DateUnlockes { get; set; }
        public string ICGPin { get; set; }
    }
}
