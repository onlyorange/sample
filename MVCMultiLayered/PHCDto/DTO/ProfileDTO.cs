using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace PHCDto.DTO
{
    public class ProfileDTO
    {
        public int? UID { get; set; }

        public int PID { get; set; }

        [Required(ErrorMessage = "*")]
        public string PetName { get; set; }
        
        public string PetBreed { get; set; }
       
        public bool PetCategory { get; set; }

        public int pcid { get; set; }

        public string PetImage { get; set; }

        [Required(ErrorMessage = "Pet photo is required")]
        public string pimg { get; set; }

        [Required(ErrorMessage = "*")]
        public string Location { get; set; }

        public int month { get; set; }
        public int date { get; set; }
        public int year { get; set; }
        [Required(ErrorMessage = "*")]
        public string DOBDate { get; set; }

        public DateTime DOB { get; set; }
        
        public string About { get; set; }

        public string FavProduct { get; set; }

        public List<FacebookFriend> friendsListing { get; set; }

        public string petcat { get; set; }

        public List<ChallengeDTO> listchdto { get; set; }

        public int pch1 { get; set; }

        public int pch2 { get; set; }

        public int pch3 { get; set; }

        public int pch4 { get; set; }

        public int pch5 { get; set; }

        public int pch6 { get; set; }

        public string pchname1 { get; set; }

        public string pchname2 { get; set; }

        public string pchname3 { get; set; }

        public string pchname4 { get; set; }

        public string pchname5 { get; set; }

        public string pchname6 { get; set; }

        public int badgecount { get; set; }

        public string FavProduct1 { get; set; }
        public string About1 { get; set; }
        public string DOBDate1 { get; set; }
        public string pimg1 { get; set; }
        public string PetName1 { get; set; }
        public string Location1 { get; set; }
        public int pcid1 { get; set; }

        public List<ProfileDTO> ldto { get; set; }

        public int secondbdg { get; set; }

    }
}
