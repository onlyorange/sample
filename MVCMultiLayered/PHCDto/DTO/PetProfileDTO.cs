using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace PHCDto.DTO
{
    public class PetProfileDTO
    {
        public int? UID { get; set; }

        public int PID { get; set; }

        [Required(ErrorMessage = "*")]
        public string PetName { get; set; }
        [Required(ErrorMessage = "*")]
        public string PetBreed { get; set; }
        [Required(ErrorMessage = "*")]
        public bool PetCategory { get; set; }

        public int pcid { get; set; }

        [Required(ErrorMessage = "Pet photo is required")]
        public string PetImage { get; set; }

        [Required(ErrorMessage = "*")]
        public string Location { get; set; }

        [Required(ErrorMessage = "*")]
        [DisplayFormat(DataFormatString = "{0:dd MMM yyyy}")]
        public DateTime DOB { get; set; }

        [Required(ErrorMessage = "*")]
        public string About { get; set; }

        public string FavProduct { get; set; }

        public List<FacebookFriend> friendsListing { get; set; }

    }


    public class FacebookFriend
    {
        public string name { get; set; }
        public string id { get; set; }
    }
}
