using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.ComponentModel.DataAnnotations;


namespace PHCDto.DTO
{
   public class RewardsDTO
    {
        public int RID { get; set; }
        public int UID { get; set; }
        public int WID { get; set; }

        public DateTime? DateUnlocked { get; set; }
        public DateTime? DateClaimed { get; set; }

        [Required(ErrorMessage = "*")]
        public string WallImage { get; set; }

        [Required(ErrorMessage = "*")]
        public string Title { get; set; }

        public List<RewardsDTO> lstrewards { get; set; }

        public string image { get; set; }
       
    }
}
