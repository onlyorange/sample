using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace PetHealthClub.Areas.PetUser.Models
{
    /// <summary>
    /// Basic model for displaying a challenge screen
    /// </summary>
    public class ChallengeCompletedViewModel
    {
        public string UserDescription { get; set; }
        public string UserImage { get; set; }
        public DateTime DateCompleted { get; set; }
    }

    /// <summary>
    /// Handles the post data for completeing a challenge
    /// </summary>
    public class ChallengePostModel
    {
        public int ChallengeId { get; set; }
        public int PetId { get; set; }
        public string userdesc { get; set; }

        public string UPLOAD_FILE_FIELD_NAME = "upload_file";
    }
}