using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace PetHealthClub.Areas.Redemption.Models
{
    /// <summary>
    /// Holds all the data we need for attempting to validate a token
    /// </summary>
    public class ValidateTokenModel
    {
        public string Token { get; set; }
        public string PrizeCode { get; set; }
    }

    /// <summary>
    /// Returns a response to the validation request
    /// </summary>
    public class ValidateTokenResponseModel
    {
        public string Status { get; set; }
        public string Message { get; set; }
    }
}