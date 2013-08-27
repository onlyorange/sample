using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using PHCDto.DTO;

namespace PHCService.Service.InterfaceContracts
{
    public interface IFbprofilePres
    {
        int userCount(FbDTO dto);

        FbDTO Save_user(FbDTO dto);

        FbDTO update_user(FbDTO dto);
        // TODO: refactor this -- the update_user method doesn't take into account all fields
        void UserUpdateForShare(FbDTO dto);

        FbDTO GetUserByFacebookId(string fbId);

        PetProfileDTO SavePetDetSignUp(PetProfileDTO dto);

        int UpdateUID(PetProfileDTO dt);
    }
}
